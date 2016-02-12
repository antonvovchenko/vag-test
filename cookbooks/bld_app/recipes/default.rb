web_app "bld_site" do
  server_name node['hostname']
  docroot '/vagrant'
  cookbook 'bld_app'
end


mysql_service 'bld_mysql' do
  port '3306'
  version '5.5'
  initial_root_password '398wjd98kjf8'
  action [:create, :start]
end


apache_site "bld_site" do
  enable true
end