execute "apt-get-update" do
  command "apt-get update"
  ignore_failure true
  action :nothing
end

web_app "bld_site" do
  server_name node['hostname']
  docroot '/vagrant'
  cookbook 'bld_app'
end

apache_site "bld_site" do
  enable true
end


# Name attribute will be ignored. Choose something that makes sense for you
#mysqld 'bld' do
#  my_cnf { 'bind-address' => '0.0.0.0' }
#end

mysqld_password 'root' do
  password '398wjd98kjf8'

  # If required, you can specify your own auth-scheme here
  # auth '-u specialuser -pmypass'
end
