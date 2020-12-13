# Workana Hiring challenge


## Backend endpoints

##### `POST http::localhost:8081/api/register` - Used to register new user  
```
// All fields required

$data = [
   'name' => 'test',
   'email'=>'test@gmail.com',
   'password' => 'secret1234'
]

response token
```

##### `POST http::localhost:8081/api/login` - Used to login  
```
// All fields required

$data = [
   'email'=>'test@gmail.com',
   'password' => 'secret1234'
]

response token
```

##### `POST http::localhost:8081/api/issue/{:issue}/join` - Used to join `{:issue}`.

```
// All fields required

$headers = [
   'Authorization'=>'Bearer '.$token
]

```

##### `GET http::localhost:8081/api/issue/{:issue}` - Used to get `{:issue}`.

```
// All fields required

$headers = [
   'Authorization'=>'Bearer '.$token
]

```
````
response json

{
"status": "voting", 
"members": [
   {"name": "florencia", "status":"voted"}, 
      {"name": "kut", "status": "waiting"}, 
      {"name": "lucho", "status": "passed"}
   ]
}

````


##### `POST http::localhost:8081/api/issue/{:issue}/vote` - Used to vote `{:issue}`.

```
// All fields required

$headers = [
   'Authorization'=>'Bearer '.$token
]

$data=[
   'vote'=>10
]

```

##### `POST http::localhost:8081/api/issue/{:issue}/end-vote` - Used to end vote .

```
// All fields required

$headers = [
   'Authorization'=>'Bearer '.$token
]

```

## Running

   - Run sudo docker-compose up -d 
   - Run sudo docker-compose exec php composer install 
   - Create an .env file inside the backend folder and copy and paste the contents of the .env.example file
   - Run sudo docker-compose exec php php artisan migrate 

## Runing Test
   - Run sudo docker-compose exec php php artisan test