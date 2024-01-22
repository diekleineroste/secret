import requests
import json
headers = {
    "Content-Type": "application/json",
}
r = requests.post("http://localhost:8080/api/auth/login", json= {"username": "example_username", "password": "example_password"})

print(r.text)
r = requests.get("http://localhost:8080/api/artpieces?page=1", headers={
    'Authorization': json.loads(r.json()['data'])['accessToken']
} )

print(r.text)