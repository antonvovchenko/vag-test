Vagrant.configure("2") do |config|

    config.vm.box      = "./vm-box/trusty-amd64-vagrant-disk1.box"
    config.vm.hostname = "bldserver"
    config.vm.boot_timeout = 500

    config.vm.network "private_network", ip: "172.16.0.4", netmask: "255.240.0.0"

    config.vm.synced_folder "./code", "/vagrant", :nfs => true

    config.vm.provider "virtualbox" do |v|
        v.memory = 512
    end

    config.vm.provision "chef_solo" do |chef|
        chef.add_recipe "apache2"
        chef.add_recipe "mysqld::mysql_install"
        chef.add_recipe "php"
        chef.add_recipe "bld_app"
    end

end
