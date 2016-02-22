execute "apt-get-update" do
  command "apt-get update"
  ignore_failure true
  action :nothing
end

include_recipe "apache2::mod_php5"
include_recipe "php::module_mysql"
include_recipe "php::module_curl"

web_app "bld_site" do
  server_name node['hostname']
  docroot '/vagrant/public'
  directory_options 'Indexes FollowSymLinks MultiViews'
  allow_override 'All'
end

file '/vagrant/.env' do
  content IO.read('/vagrant/.env.vagrant')
  action :create
end

execute 'artisan-cache-clear' do
  cwd '/vagrant'
  command 'php artisan cache:clear'
end
execute 'artisan-key-generate' do
  cwd '/vagrant'
  command 'php artisan key:generate'
end
execute 'artisan-migrate' do
  cwd '/vagrant'
  command 'php artisan migrate'
end
execute 'artisan-optimize' do
  cwd '/vagrant'
  command 'php artisan optimize'
end
execute 'artisan-config-cache' do
  cwd '/vagrant'
  command 'php artisan config:cache'
end

apache_site "bld_site" do
  enable true
end

mysqld_password 'root' do
  password '398wjd98kjf8'

  # If required, you can specify your own auth-scheme here
  # auth '-u specialuser -pmypass'
end

mysql2_chef_gem 'default' do
  action :install
end

mysql_database 'bld' do
  connection(
    :host     => '127.0.0.1',
    :username => 'root',
    :password => '398wjd98kjf8'
  )
  action :create
end