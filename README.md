Groove-PHP-Wrapper
==================

A PHP wrapper for Groove[ http://groovehq.com ]

Groove API docs can be found here: https://www.groovehq.com/docs

Initializing
------------------

```php
include_once 'groove.class.php';

$groove = new groove('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); // your api key here
```

Methods
------------------

- getTickets(_optional array_('assignee'=>'assignee', 'customer'=>_customer_, 'page'=>_page_, 'per_page'=>_per_page_, 'state'=>_state_))
- getTicket(_ticket_number_)
- getTicketState(_ticket_number_)
- updateTicketState(_ticket_number_, 'state'=>_state_)
- getTicketMessages(_ticket_number_, ['page'=>page, 'per_page'=>_per_page_])
- getTicketMessage(_message_id_)
- createTicketMessage(_ticket_number_, _body_, ['note'=>_note_])
- getCustomers(['page'=>_page_, 'per_page'=>_per_page_])
- getCustomer(_customer_email_)
- updateCustomer(_customer_email_, ['email'=>_email_, 'first_name'=>_first_name_, 'last_name'=>_last_name_, 'about'=>_about_, 'twitter_username'=>_twitter_username_, 'title'=>_title_, 'company_name'=>_company_name_, 'phone_number'=>_phone_number_, 'location'=>_location_, 'linkedin_username'=>_linkedin_username_])
- getAgent(_agent_email_)

Example
------------------
```php
$groove->updateTicketState(1, 'opened');
// or possibly
$groove->createTicketMessage(1, "Thankyou George\n\nWe will get back with you as soon as possible.", ['note'=>'text123']);
```

Notes
------------------
If any changes are needed, feel free to contact me.
