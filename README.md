# 🌍 Froxlor DDNS Endpoint
I made this little project to be able to update my Froxlor DNS entries dynamically to use Froxlor as my DDNS provider on my Synology DiskStation. This project provides a HTTP endpoint which can be called to update a specific host for a specific domain.

It makes sure that no other A record for that host on that domain exists before it creates the DDNS one. If one or multiple A records for that host exist, they'll be deleted.

Please note that this might be dirty. Error messages are not that helpful at the moment and I'm probably not going to maintain it as long as it works as intended.


## ⚡ Features
* Allows multi-user authentication
* Domain separation per user
* Ensures no duplicate entries


## 🛠 Installation
Make sure that you have PHP 7 or newer installed on your system. I've developed and tested it with PHP 7.4. You also need the cURL extension. If everything is ready, follow these instructions:

1. [Download the repository](https://github.com/tinyoverflow/froxlor-ddns-endpoint/archive/refs/heads/master.zip) or clone it via git.
2. Upload it to your webserver.
3. Configure it as explained below.


## ⚙ Configuration
1. Copy the `config.inc.php.example` file to `config.inc.php`.
2. Provide your Froxlor API endpoint URL and your API key & secret.
3. Configure your users. The example provides one user called `admin`.
4. Set a token for your users. Make sure it's long and secure.
5. Configure the domains. The format is: `'domain.tld' => ttl in seconds`.
