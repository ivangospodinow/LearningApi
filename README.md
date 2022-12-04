 Fork of https://github.com/ivangospodinow/microblog
 
 # Some determinations:
  - Maximum learning time per day is 86400 seconds (1 day).
  - Ideal means average learning per second. This way required learning for not full days will be proportinaly less (prorated).
  - It should be ok to have progress on a course that is not yet started. May be the periods were shifted for some reason.
  - If overdue needed daily learning become maximum daily learning instead of ideal.
  - if due time is 23:59:59 then a second will be added for more clear calculations. It will be assumed as one day of 86400 seconds.

# Requirements
- php 7.4
- composer
- git

# Installation
```sh
git clone https://github.com/ivangospodinow/LearningApi.git ./LearningApi
cd LearningApi
# install dependencies
composer install
# starts the backend server, timeout is 1 hour
composer run-script localhost-backend
````

# Testing
```sh
composer run-script tests
```

## Commands
```sh
# starts the backend server, timeout is 1 hour
composer run-script localhost-backend
# runs backend phpunit tests
composer run-script tests
````

# Task: Progress status evaluation and estimation API

depending on start date, end date, current progress and total learning time

**Story**:

The goal is to create an API that determines if it’s achievable to go through the duration of learning content (in reality video time) with a current learning progress until a due date. Depending on the difference between the actual and ideal progress at the time of the call the API returns if the user is on track or not.

**Endpoint design:**

- The endpoint should be RESTful and naming recommendations compliant
- The endpoint should be designed following the best practices
- Request method - GET

**Request parameters:**

- Course duration in seconds → integer
- Current learning progress in percentage → integer
- Assignment creation date → datetime (RFC3339)
- Due date → datetime (RFC3339)

The names of all request parameters names are not predefined and must be proposed by the developer.

**Implementation requirements:**

- Implementation should use business logic class
- Validate all input parameter values for cast type and plausibility.
- If the input data is invalid, the API must return error code and message as you see appropriate
- If the input data is valid, the API must process the input and respond with json encoded data in the body. Response data properties should be:
  - progress\_status: string → “on track” | “not on track” | “overdue”
  - expected\_progress: integer → the expected progress value at the moment
  - needed\_daily\_learning\_time: integer → learning time per day in seconds
- Define and handle edge cases

**Criteria for “progress\_status” values “on track”, “not on track” or “overdue”**

- “on track” is when the current learning progress is equal or greater than the ideal progress expected at the time, when the API was requested
- “not on track” is when the current learning progress is less than the ideal progress expected at the time, when the API was requested
- “overdue” is when the due date is in the past already and the progress is less than 100%

**Definition of result field “expected\_progress”:**

The field “expected\_progress” contains the ideal progress percentage that is expected to have been achieved at the time of the request.

**Definition of result field “needed\_daily\_learning\_time”:** Daily learning time needed to achieve the goal.

**General hints and constraints:**

- Usage of PHP framework is required - any modern framework is acceptable.
- TDD (partial or complete) is a bonus.
