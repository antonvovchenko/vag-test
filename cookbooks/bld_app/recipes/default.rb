web_app "bld_site" do
  server_name node['hostname']
  docroot '/vagrant'
  cookbook 'bld_app'
end

apache_site "bld_site" do
  enable true
end