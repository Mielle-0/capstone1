import os
import joblib
import numpy as np
from fastapi import FastAPI, HTTPException, Security, Depends, status
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from pathlib import Path
from typing import List, Dict, Any
from contextlib import asynccontextmanager
import pandas as pd

# Cloud repo for models
# REPO_ID = "Mielle-0/department-prediction-v4"

# When Local models downloaded
BASE_DIR = Path(__file__).resolve().parent
MODEL_FILENAME = BASE_DIR / "models" / "department" / "lr_count_pipeline.joblib"
MASK_FILENAME = BASE_DIR / "models" / "department" / "branch_to_dep_map.joblib"

assets = {"pipeline": None, "mask": None}

@async_contextmanager
async def lifespan(app: FastAPI):
    try:
        # Check if files exist before loading
        if not MODEL_PATH.exists() or not MASK_PATH.exists():
            raise FileNotFoundError(f"Model files not found at {MODEL_PATH.parent}")

        # Load Pipeline and Mask
        assets["pipeline"] = joblib.load(MODEL_PATH)
        assets["mask"] = joblib.load(MASK_PATH)
        
        print(f"✅ Local Assets Loaded: {MODEL_PATH.name} and {MASK_PATH.name}")
    except Exception as e:
        print(f"❌ Startup Error: {e}")
    yield
    assets.clear()
app = FastAPI(title="Modern Multi-Model API", lifespan=lifespan)


@app.get("/health")
async def health_check():
    return {
        "status": "online",
        "pipeline_loaded": assets["pipeline"] is not None,
        "mask_loaded": assets["mask"] is not None
    }

class PredictRequest(BaseModel):
    branch: str
    details: str


class PredictionEntry(BaseModel):
    department: str
    probability: float

class PredictResponse(BaseModel):
    used_model: str
    top_3: List[PredictionEntry]



@app.post("/predict", response_model=PredictResponse)
async def predict(request: PredictRequest):

    pipeline = assets.get("pipeline")
    branch_map = assets.get("mask")

    if not pipeline:
        raise HTTPException(status_code=404, detail="Pipeline not loaded")
    if not branch_map:
        raise HTTPException(status_code=404, detail="Branch mask not loaded")

    clean_branch = request.branch.strip()
    clean_details = request.details.strip()

    if not clean_details:
        raise HTTPException(status_code=400, detail="Feedback details cannot be empty")

    input_df = pd.DataFrame([{
        'branch': clean_branch, 
        'details_cleaned': clean_details
    }])

    try:
        all_probs = pipeline.predict_proba(input_df.fillna(''))[0]
        classes = pipeline.classes_

        allowed_deps = branch_map.get(request.branch, [])

        masked_candidates = []
        for idx, dep_id in enumerate(classes):
            if dep_id in allowed_deps:
                masked_candidates.append({
                    "department": str(dep_id),
                    "probability": float(all_probs[idx])
                })

        masked_candidates.sort(key=lambda x: x['probability'], reverse=True)

        results = masked_candidates[:3] # Selects only 3

        return PredictResponse(used_model=str(MODEL_FILENAME), top_3=results)
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Prediction error: {str(e)}")
