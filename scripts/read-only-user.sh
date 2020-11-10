echo "Parsing input..."
database=$1
username=$2
password=$3

echo "Creating read-only user..."
sudo -i -u postgres psql -c "CREATE USER $username WITH PASSWORD '$password'";
sudo -i -u postgres psql -c "GRANT CONNECT ON DATABASE $database TO $username";
sudo -i -u postgres psql -c "GRANT USAGE ON SCHEMA public TO $username";

echo "Granting read-only access to [blocks] table..."
sudo -i -u postgres psql -d $database -c "GRANT SELECT ON blocks TO $username";

echo "Granting read-only access to [rounds] table..."
sudo -i -u postgres psql -d $database -c "GRANT SELECT ON rounds TO $username";

echo "Granting read-only access to [transactions] table..."
sudo -i -u postgres psql -d $database -c "GRANT SELECT ON transactions TO $username";

echo "Granting read-only access to [wallets] table..."
sudo -i -u postgres psql -d $database -c "GRANT SELECT ON wallets TO $username";
