Vagrant.configure("2") do |config|
  config.vm.box = "debian/stretch64"
    
  config.vm.network "forwarded_port", guest: 80, host: 9090
    
  config.vm.provision :ansible do |ansible|
    ansible.playbook = "playbook.yml"
  end
end

