
Les vues (templates)

Pour afficher une vue sans passer par un module : appliquer la fonction showView sur le template en cours ($this-> showView)

Pour afficher la vue standard d'un module (nameModuleView.php) il n'y a qu'a laissé faire le base controller,
(qui fera un  $this->getModule()->setTemplate(new template($this->module));)

Si vous voulez le personnalisez utilisez : 
            $this->getModule()->setTemplate(new template($this->module,'nameView'));
dans le controller de votre module
Ceci permet de faire un test sur l'url pour afficher differente vues d'un meme module.

Pour ajouter les parametres passé en argument dans l'url il suffit d'appliquer le code :
        $datas = $this->core->getDatas();
        $this->addDatasModule('view', $datas['Parameter'][0]);
->pousse les données de l'url dans les données du module, elles pourront etre reutilisé plus tard par les autres modules

Pour recuperer les données d'un modules precedement lancé appliquez la fonction getDatasModule sur le modules ($this->getDatasModule('User');)
        $Parameter = $this->getDatasModule('Parameter');
        $view = $Parameter['view'];
->retourne la vue passé en argument pour le module user (login, inscription, etc..)

Par defaut tous les modules utilise le layout "default" pour le modifier, a la creation du controller, modifier le layout avec :
