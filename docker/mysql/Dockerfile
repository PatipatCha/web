FROM mysql/mysql-server:5.5

COPY ["db_data/ltm_translations.sql", "/ltm_translations.sql"]
COPY ["db_data/migrations.sql", "/migrations.sql"]
COPY ["db_data/password_resets.sql", "/password_resets.sql"]
COPY ["db_data/sessions.sql", "/sessions.sql"]
COPY ["db_data/users.sql", "/users.sql"]
COPY ["start_script.sh", "/start_script.sh"]

EXPOSE 3306