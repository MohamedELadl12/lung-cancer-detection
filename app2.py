from fastapi import FastAPI, File, UploadFile
from fastapi.responses import JSONResponse
import uvicorn
from PIL import Image
import torch
import torch.nn as nn
import torchvision.transforms as transforms
from torchvision import models
import io

# Initialize app
app = FastAPI()

# Load model
device = torch.device("cuda" if torch.cuda.is_available() else "cpu")

model = models.resnet50(pretrained=False)
model.fc = nn.Linear(model.fc.in_features, 3)  # 3 classes
model.load_state_dict(torch.load("resnet_lung_cancer.pth", map_location=device))
model = model.to(device)
model.eval()

# Class names
class_names = ["Benign", "Malignant", "Normal"]

# Preprocessing
transform = transforms.Compose([
    transforms.Resize((224, 224)),  # ResNet input size
    transforms.ToTensor(),
    transforms.Normalize([0.485, 0.456, 0.406],  # ImageNet mean
                         [0.229, 0.224, 0.225])  # ImageNet std
])

@app.post("/predict/scan")
async def predict(file: UploadFile = File(...)):
    """
    Predict lung cancer diagnosis from a chest X-ray image.

    Args:
    file: The chest X-ray image in JPEG format.

    Returns:
    A JSON object with the following keys:
    class: The predicted class (Benign, Malignant, or Normal).
    confidence: The confidence level of the prediction (a value between 0 and 1).

    Raises:
    500 Internal Server Error if an error occurs during prediction.
    """
    try:
        image_bytes = await file.read()
        image = Image.open(io.BytesIO(image_bytes)).convert("RGB")
        image = transform(image).unsqueeze(0).to(device)

        with torch.no_grad():
            outputs = model(image)
            _, predicted = torch.max(outputs, 1)
            class_idx = predicted.item()
            confidence = torch.softmax(outputs, dim=1)[0][class_idx].item()

        return {
            "class": class_names[class_idx],
            "confidence": round(confidence, 4)
        }
    except Exception as e:
        return JSONResponse(status_code=500, content={"error": str(e)})


if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=5000)
