## Rules for contributors

* Git commits have to be [signed](https://git.mdns.eu/nextcloud/passwords/wikis/Developers/Contributing/Verify-Git-Commits)
* Keep any discussions civil

## Rules for issues

* Issues not following the template will be closed without review
* Issues with the browser extension have to be opened in this [repository](https://github.com/marius-wieschollek/passwords-webextension)
* For questions and support, visit the [forum](https://help.nextcloud.com/c/apps/passwords)

## Set up the local environment
#### Requirements
* Internet Connection
* [Node](https://nodejs.org/)
* [Docker](https://store.docker.com/search?type=edition&offering=community)
* [Docker Compose](https://docs.docker.com/compose/install/#install-compose)
* [mkcert](https://github.com/FiloSottile/mkcert)

#### Project Setup
1. Open a shell on your computer and navigate to the projects directory
2. Run `npm install`
3. Run `npm run build`
4. If you have [mkcert](https://github.com/FiloSottile/mkcert), run `npm run mkcert`;
5. Run `npm run start`
6. Open your browser and type [https://localhost/](https://localhost/)
7. Log in with the username `admin` and the password `admin`
8. You can now access the [app](http://localhost/index.php/apps/passwords)

#### Sample Data
The docker setup comes with two test users `max` and `erika` with the password `PasswordsApp`.
All users have a set of sample data installed.
You can also download a [file with sample data](https://git.mdns.eu/nextcloud/passwords/wikis/Developers/_files/SamplePasswords.json) from the wiki and can import it on the [backups page](http://localhost/index.php/apps/passwords#/backup).

#### API Debugging
We offer an [import file](https://git.mdns.eu/nextcloud/passwords/wikis/Developers/_files/insomnia-workspace.json) for the free API debugging tool [Insomnia](https://insomnia.rest/).

#### Helpful commands
* `npm run watch` - Create development build of JS/CSS files and watch for changes
* `npm run start` - Start the docker server. Use `postgres:start`, `sqlite:start`, `ldap:start` or `saml:start` to include these services
* `npm run stop` - Stop the docker server. Use `postgres:stop`, `sqlite:stop`, `ldap:stop` or `saml:stop` to include these services
* `npm run cron` - Execute cron jobs
* `npm run shell` - Open a shell in the container
* `npm run shell:root` - Open a root shell in the container
* `npm run down` - Remove docker containers and volumes. Use `postgres:down`, `sqlite:down`, `ldap:down` or `saml:down` to include these services
* `npm run docker:build` - Start docker and build images
* `npm run build` - Create production build of JS/CSS files
* `npm run build:nightly` - Create nightly build of JS/CSS files
* `npm run selenium` - Start Selenium server for frontend testing
* `npm run handbook` - Create screenshots for the manual
* `docker-compose -f docker-compose.yml -f docker/configs/<service>.yml up -d` starts the system with additional services
* `docker exec -u www-data passwords-php /var/www/html/occ  config:system:set loglevel --value=0 --type=int` - Set loglevel to info

#### Services
 - [https://localhost](https://localhost) is Nextcloud (with the app)
 - [https://localhost:8080](https://localhost:8080) is PhpMyAdmin (for the MySQL database)
 - [https://localhost:1080](https://localhost:1080) is MailHog (for Emails)
 - [https://localhost:8081](https://localhost:8081) is pgAdmin (for the Postgres database, user `admin@nextcloud`, password `nextcloud`)
 - [https://localhost:9090](https://localhost:9090) is PhpLdapAdmin (optional, user `cn=admin,dc=nextcloud`, password `nextcloud`)
 - [https://localhost:9091](https://localhost:9091) is Keycloak (for SAML, optional, user `admin`, password `nextcloud`)

#### LDAP Setup
 - Open [PhpLdapAdmin](https://localhost:9090) and log in
 - Create a new entry with the template "Generic: Posix Group"
 - Create a new Entry with the template "Generic: User Account" as child entry of the group
 - Enable "LDAP user and group backend"
 - Go to "Settings" > "LDAP/AD Integration"
 - Enter `openldap` as server and `389` as port
 - Enter `cn=admin,dc=nextcloud` as User-DN and `nextcloud` as password
 - Click on "Save Credentials"
 - Enter `dc=nextcloud` as Base DN
 - Click on "Continue"
 - Select "inetOrgPerson" and "posixAccount" as object classes
 - Click on "Continue"
 - Click on "Continue"
 - Select "posixGroup" as object classes

#### Telegram 2FA Gateway Setup
 - Install the "Two-Factor Gateway" app from the app store
 - Run `npm run shell` from the terminal
 - Run `./occ twofactorauth:gateway:configure telegram`
 - Enter `703713714:AAGUFZfIINIm4_0Qo3i9qHNZVfduDbwHtnY`
 - Chat with the [get_id_bot](tg://resolve?domain=get_id_bot) to get your Telegram id

#### Keycloak SAML Setup
 - Open [Keycloak](https://localhost:9091) and log in
 - Follow [this guide](https://stackoverflow.com/a/48400813) ([internet archive link](https://web.archive.org/web/20200116192513/https://stackoverflow.com/questions/48400812/sso-with-saml-keycloak-and-nextcloud/48400813))

#### First steps
* [Issues for Beginners](https://github.com/marius-wieschollek/passwords/labels/for%3Astarters)
* [Developer Manual](https://git.mdns.eu/nextcloud/passwords/wikis/Developers/Index)