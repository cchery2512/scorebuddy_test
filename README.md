### Before run the app you going to need

- Install Docker

## For run the application write this command in the console:
- docker-compose build && docker-compose up -d

## Important: the port of the application is the 14000 => http://127.0.0.1:14000

### NextDrawDayCommand
## Description
- Gets the next day the Irish Lottery draw will take place.

## Signature
- app:next:valid:draw:date [date] (OPTIONAL)

## Example
- Run command: docker-compose exec app php bin/console app:next:valid:draw:date 2023-07-29T05:29:55+00:00


### Run the 'next-valid-draw-date' API

## Description
- Gets the next day the Irish Lottery draw will take place.

## Signature
- GET /api/next-valid-draw-date=[date] (OPTIONAL)
## Example
- Put in you postman: http://127.0.0.1:14000/api/next-valid-draw-date?date=2023-07-29T05:29:55+00:00 in GET


## Note:
- Once the docker container has been created, please wait a few more seconds to test the application, some services take a few more seconds to deploy.
# scorebuddy_test
