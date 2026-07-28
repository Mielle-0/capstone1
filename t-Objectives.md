To create a feedback classification system powered by AI that can
automatically sort and direct feedback messages sent to the
University of Mindanao's feedback management system.

1.3.2 Specific Objectives
To achieve the general objective, the proponents will do the
following:

    1.3.2.1 To implement a machine learning classification module
    that can categorize feedback into five predefined types: Inquiry,
    Concern, Suggestion, Complaint, and Commendation.

    1.3.2.2 To automatically suggest routing of university department
    based on text features and category type using department-specific
    training data.

    1.3.2.3 To quantify the confidence scoring as the basis of
    intervention and automatically capture the action taken.

    1.3.2.4 To ensure the module can handle multilingual input,
    specifically English, Filipino, and Cebuano, reflecting the
    diversity of the users interacting with the feedback system.

    1.3.2.5 To evaluate the model’s performance using standard
    metrics such as accuracy, precision, recall, and F1-score across
    each classification label and department routing output.



1.3.2.1
    - "categorize feedback into five predefined types"
        - ACHIEVED

1.3.2.2
    - "routing of department based on text features and ``category type``"
        - category type is not needed for department prediction

1.3.2.3
    - "confidence scoring as basis of intervention" 
        - confidence thresholding
        - auto route to departments unless it is below a set percentage
    - "capture action taken"
        - logging

1.3.2.4
    - "module can handle multilingual input"
        - uses Character N-grams
        - ACHIEVED on Department Prediction Model

1.3.2.5
    - "evaluate the model's performance"
        - ACHIEVED
    

Objective 1.3.2.1 (Categories): Fully covered by predicted_category and verified_category.

Objective 1.3.2.2 (Department Routing): Covered by your ML pipeline and safely stored in prediction_candidates (model's guesses) and verified_dept_ids (human truth array).

Objective 1.3.2.3 (Intervention): Perfectly handled by requires_intervention and the action_taken cluster.

Objective 1.3.2.4 (Multilingual): Addressed offline by your training data. (Side note: If you want to track this in the database, you could add a detected_language column later, but it's not strictly required for the schema).

Objective 1.3.2.5 (Evaluation Metrics): Because you have the AI's guesses and the Human's verified truth in the same database, you can easily calculate Accuracy, Precision, Recall, and F1 dynamically using SQL or Laravel collections.