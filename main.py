from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import joblib
import pandas as pd
import numpy as np
import pickle
import uvicorn


app = FastAPI(title="Lung Cancer Prediction API")

# Load the trained model (adjust the path as needed)

model = joblib.load("C:/Users/HP-01/Lung-Cancer-Prediction/lung_cancer_predicate/lung-cancer-gradint.joblib")

# Define input data model
class LungCancerInput(BaseModel):
    GENDER: str
    AGE: int
    SMOKING: int
    YELLOW_FINGERS: int
    ANXIETY: int
    PEER_PRESSURE: int
    CHRONIC_DISEASE: int
    FATIGUE: int
    ALLERGY: int
    WHEEZING: int
    ALCOHOL_CONSUMING: int
    COUGHING: int
    SHORTNESS_OF_BREATH: int
    SWALLOWING_DIFFICULTY: int
    CHEST_PAIN: int

# Define response model with an optional message
class PredictionResponse(BaseModel):
    probability: float  # This will be the percentage
    message: str = None  # Message if probability > 50

@app.post("/predict", response_model=PredictionResponse)
async def predict(data: LungCancerInput):
    """
    Predict the probability of a patient having lung cancer based on input data.

    This endpoint takes a JSON payload with the following fields:

    - GENDER: string, either 'M' or 'F'
    - AGE: integer
    - SMOKING: binary, 1 or 2
    - YELLOW_FINGERS: binary, 1 or 2
    - ANXIETY: binary, 1 or 2
    - PEER_PRESSURE: binary, 1 or 2
    - CHRONIC_DISEASE: binary, 1 or 2
    - FATIGUE: binary, 1 or 2
    - ALLERGY: binary, 1 or 2
    - WHEEZING: binary, 1 or 2
    - ALCOHOL_CONSUMING: binary, 1 or 2
    - COUGHING: binary, 1 or 2
    - SHORTNESS_OF_BREATH: binary, 1 or 2
    - SWALLOWING_DIFFICULTY: binary, 1 or 2
    - CHEST_PAIN: binary, 1 or 2

    Returns a JSON response with the following fields:

    - probability: float, the probability of the patient having lung cancer
    - message: string, a message indicating whether the patient must enter their CT scan
    """
    try:
        # Convert input data to DataFrame
        input_data = pd.DataFrame([data.dict()])
        
        # Preprocess the input data
        # Convert GENDER to match model's encoding (M=1, F=0)
        input_data['M'] = input_data['GENDER'].map({'M': 1, 'F': 0})
        input_data = input_data.drop(columns=['GENDER'])
        
        # Define feature names expected by the model
        model_feature_names = [
            'M', 'AGE', 'SMOKING', 'YELLOW_FINGERS', 'ANXIETY', 
            'PEER_PRESSURE', 'CHRONIC DISEASE', 'FATIGUE ', 'ALLERGY ', 
            'WHEEZING', 'ALCOHOL CONSUMING', 'COUGHING', 
            'SHORTNESS OF BREATH', 'SWALLOWING DIFFICULTY', 'CHEST PAIN'
        ]
        
        # Rename input columns to match model feature names
        input_feature_names = [
            'M', 'AGE', 'SMOKING', 'YELLOW_FINGERS', 'ANXIETY', 
            'PEER_PRESSURE', 'CHRONIC_DISEASE', 'FATIGUE', 'ALLERGY', 
            'WHEEZING', 'ALCOHOL_CONSUMING', 'COUGHING', 
            'SHORTNESS_OF_BREATH', 'SWALLOWING_DIFFICULTY', 'CHEST_PAIN'
        ]
        rename_dict = dict(zip(input_feature_names, model_feature_names))
        input_data = input_data.rename(columns=rename_dict)
        
        # Reorder columns to match training data
        input_data = input_data[model_feature_names]
        
        # Validate binary features (must be 1 or 2)
        binary_features = model_feature_names[2:]  # Exclude M and AGE
        for feature in binary_features:
            if input_data[feature].iloc[0] not in [1, 2]:
                raise HTTPException(status_code=400, detail=f"Invalid value for {feature}. Must be 1 or 2.")
        
        # Make prediction
        prediction = model.predict(input_data)[0]
        probability = model.predict_proba(input_data)[0][1]  # Probability of positive class
                
        # Multiply probability by 100 to get percentage
        probability_percentage = round(probability * 100, 2)        
        # Set message if probability percentage is greater than 50
        message = "You must enter your CT scan." if probability_percentage > 50 else None
        
        # Return the response
        return PredictionResponse(
            probability=probability_percentage,
            message=message
        )
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))



if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=5001)
