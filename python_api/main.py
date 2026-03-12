import os
import joblib
import numpy as np
from fastapi import FastAPI, HTTPException, Security, Depends, status
from fastapi.security.api_key import APIKeyHeader
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from pathlib import Path
from typing import List, Dict, Any
from contextlib import asynccontextmanager
from dotenv import load_dotenv
import pandas as pd
from huggingface_hub import hf_hub_download

# SECURITY CONFIG
load_dotenv()
API_KEY = os.getenv("MY_API_SECRET_KEY")
API_KEY_NAME = "X-API-KEY"
api_key_header = APIKeyHeader(name=API_KEY_NAME, auto_error=False)

origins_raw = os.getenv("ALLOWED_ORIGINS", "*")
ALLOWED_ORIGINS = [origin.strip() for origin in origins_raw.split(",")]

async def get_api_key(header_key: str = Security(api_key_header)):
    if header_key == API_KEY:
        return header_key
    raise HTTPException(
        status_code=status.HTTP_403_FORBIDDEN, 
        detail="Could not validate credentials"
    )

REPO_ID = "Mielle-0/department-prediction-v4"
MODEL_FILENAME = "lr_count_pipeline.joblib"
MASK_FILENAME = "branch_to_dep_map.joblib"

assets = {"pipeline": None, "mask": None}

async def lifespan(app: FastAPI):       # Uses models from cloud from cloud
    try:
        # 1. Download/Load Pipeline
        model_path = hf_hub_download(repo_id=REPO_ID, filename=MODEL_FILENAME)
        assets["pipeline"] = joblib.load(model_path)
        
        # 2. Download/Load Branch Mask
        mask_path = hf_hub_download(repo_id=REPO_ID, filename=MASK_FILENAME)
        assets["mask"] = joblib.load(mask_path)
        
        print(f"✅ Assets Loaded: {MODEL_FILENAME} and {MASK_FILENAME}")
    except Exception as e:
        print(f"❌ Startup Error: {e}")
    yield
    assets.clear()

app = FastAPI(title="Modern Multi-Model API", lifespan=lifespan)

app.add_middleware(
    CORSMiddleware,
    allow_origins=ALLOWED_ORIGINS, 
    allow_methods=["POST", "GET"],
    allow_headers=["*"],
)


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
async def predict(request: PredictRequest, api_key: str = Depends(get_api_key)):

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
