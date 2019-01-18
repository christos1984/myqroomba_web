# Vacuum Robot API

API for interacting with MyQ Vacuum Robot.

## How to interact with API

API provides just one Endpoint, `/action/start` responding to HTTP POST. Requires a valid configuration file like below and it will respond with JSON letting us know the visited cells, cleaned cells, final position and battery of the robot.
```
{
  "map": [
    ["S", "S", "S", "S"],
    ["S", "S", "C", "S"],
    ["S", "S", "S", "S"],
    ["S", "null", "S", "S"]
  ],
  "start": {"X": 3, "Y": 0, "facing": "N"},
  "commands": [ "TL","A","C","A","C","TR","A","C"],
  "battery": 80
}
```

1. Copy the app to a PHP enabled web server - for testing it would be OK to launch the PHP's internal server `php -S localhost:8002 -t public`
2. Launch an HTTP POST request (via client code, Postman, cURL or anything else) like this:
```
   curl -X POST \
  http://localhost:8002/action/start \
  -H 'Content-Type: application/json' \
  -d '{
  "map": [
    ["S", "S", "S", "S"],
    ["S", "S", "C", "S"],
    ["S", "S", "S", "S"],
    ["S", "null", "S", "S"]
  ],
  "start": {"X": 3, "Y": 0, "facing": "N"},
  "commands": [ "TL","A","C","A","C","TR","A","C"],
  "battery": 80
}'
```
3. The output should be the one we described above
