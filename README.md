## Bunq Chat Assignment 🦄


## 1.	Framework Selection
I was thinking about not using any framework and constructing from very scratch but then I decided to use Slim Framework just for routing, and couple of open source libraries. If you ask why not a more popular one like Zend or Laravel, they ship with lots of features out-of-box and I wanted to build the most part.


## 2.	Serving

Just `composer update` and you are ready to go! You can put files in `apache2` or serve in place via `composer start`.

## 3.	Dependencies
PHP >= 5.5, <7
#### Composer:
* **Slim:** Routing
* **PHP Unit:** Testing
* **Elequent ORM:**  Database Manager
* **Phinx:** Database Migrations
* **Requests:** Http Requests
#### PHP Extensions: 
* SQLITE_PDO
* MOD_REWRITE

## 3. Project Structure
Unlike big frameworks slim doesn’t require a boilerplate with tons of useless files (again like laravel, zend etc) , so every file is tailor-made for this project. Feel free to see around.

#### 3.a.	Config
Configurations for libraries and different envs like prod and test

#### 3.b. Models
ORM Models are database mappings handled via php classes.
I have 5 models:
* **User** 
* **Message**
* **Group** 
* **Accesstoken** (see Authentication below) 
* **Report** (see Reports below)
 
#### 3.c. Migrations
Tables are created and uptadated with migrations.Phinx -originally developed for cakephp- is very good library to create migrations. 

#### 3.d. Controllers &amp; Routing
app.ph contains routing informaton. if a  request path and method matches a view than right controller and method(as in funciton) is called.


#### 3.e. Exceptions and Logs
If a response should be terminated before it is completed, an APIException is thrown to intercept with the view. 
This way we make sure handling of view is stopped immediately. 

Api exceptions are not errors they are interceptors so they are expected and non-dangerous. Other exceptions (ErrorException etc)
result in 500 repsonses and logged


### f. Tests
Test folder contains live server tests. 
To run the tests you first need a live-test-server. The reason I prefered test is live test is envorinment dependant
so you can run the test get the exact outcomes that would happen that specific env. These results may differ in different envs.

Not that, live-test-server and prod server may run along and they wouldn't disturb each other.

## 4. Functionality

#### 4.a Authentication
When a user registers or logins an accesstoken is generated and given to user. 
This token is later used for all actions requiring authentication. 
For example user **can not** use password to send a message.
Devices are expected to store the accesstoken and not password.

Each login creates a different token to be stored independantly from other devices.
A token is not given second time after it is generated. Client should store the tokens.

Indpeendant token have two adventages:
* Tokens can be invalidated after some time.
* Logging out one device wouldn't reuire logging out from another

Logging out clears current token.
Logging out from all clears all tokens.

In order to autheriza you should add `Authorization` header to your request.
```
Authorization: ACCESSTOKEN
```


#### 4.b Groups
A user can create a group, invite people in, kick them out or leave a group.
Users can also send messages in groups.


#### 4.c Messages
There are two types of chats Personal and Group:

Starting a new personal chat or continuing is essentially the same. 
This way to create a new chat you don’t need to check if there already is an existing one so you
get to start to chat in just one request instead of 3 (check if exist, create if not, send the actual message)


In order to send a group message you must be a member of it (d'uuuuh)

There is only one message table so group messages and personal ones share same format.
There are different adventages, one of them is that an update to messages(ex WhatsApp recently created reply to message feature) will apply to both.


Messages are served with **Reports**. Reports contain: delivered_date, seen_date, and a user.
 
Every time a message is sent the same amount of new reports is creaed as the number of target receivers.

Thanks to reports:
* We don’t have it but if a someone wants to delete a message and deletes the message, message is deleted from both ends, but if delete the report than the other party gets to keep the message.
* If someone new comes to the group then we keep old messages safe and private from there.
* We get to see who sees message and who didn’t in a group.






## 5. Demo &amp; Docs

I deployed a demo on [http://bunq-chat.aliahmetbingul.com/](http://bunq-chat.aliahmetbingul.com/)

There is doc dynamically created by views at /docs/ endpoint. In docs you can find endpoints, descriptions, how to call them  and you can actually call them.

For demo docs see [http://bunq-chat.aliahmetbingul.com/docs/](http://bunq-chat.aliahmetbingul.com/docs/)



