UPDATE accounts SET account_url = CONCAT('http://profiles.us.playstation.com/playstation/psn/visit/profiles/', username, '/') WHERE service LIKE 'psn';