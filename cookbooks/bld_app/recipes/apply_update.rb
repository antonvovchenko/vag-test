remote_file "Chef::Config[:file_cache_path]/update1.tar.gz" do
  url 'https://dl.dropboxusercontent.com/u/8362142/bld-sh/update1.tar.gz'
end


bash "unpack code" do

   code <<-EOS
   tar xzf #{Chef::Config[:file_cache_path]/update1.tar.gz} --strip-components=1 -C /usr/local/jdk-7.2
   EOS

end