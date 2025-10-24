import zipfile
import tempfile
import os
import time
import json
import sys
from transformers import AutoTokenizer, AutoModelForSequenceClassification, pipeline

if len(sys.argv) < 2:
    print(json.dumps({"error": "Missing text input"}))
    sys.exit(1)

text = sys.argv[1]
zip_path = "C:/xampp/htdocs/backend/model/best_roberta_model_94recall-20250928T004912Z-1-001.zip"

start_time = time.time()

try:
    with tempfile.TemporaryDirectory() as temp_dir:
        with zipfile.ZipFile(zip_path, 'r') as zip_ref:
            zip_ref.extractall(temp_dir)

        model_folder = temp_dir
        for root, dirs, files in os.walk(temp_dir):
            if "config.json" in files:
                model_folder = root
                break

        tokenizer = AutoTokenizer.from_pretrained(model_folder)
        model = AutoModelForSequenceClassification.from_pretrained(model_folder)
        classifier = pipeline("sentiment-analysis", model=model, tokenizer=tokenizer)
        result = classifier(text)[0]

    response_time = round(time.time() - start_time, 3)

    label_map = {
        "LABEL_0": "negative",
        "LABEL_1": "neutral",
        "LABEL_2": "positive"
    }
    final_label = label_map.get(result["label"], result["label"])

    print(json.dumps({
        "label": final_label,
        "score": round(float(result["score"]), 3),
        "response_time": response_time
    }))

except Exception as e:
    print(json.dumps({"error": str(e)}))
