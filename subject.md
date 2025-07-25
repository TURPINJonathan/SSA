# Super Secret Agency

Vous venez d’être recruté par la Super Secret Agency (SSA), en tant qu’ingénieur informatique dans leur branche informatique, une mission vous a été confiée de développer un logiciel permettant d’organiser leurs opérations les plus secrètes !   
Pour vous aider dans votre mission nous vous conseillons d’utiliser notre Tech stack actuelle : 
- API platform, 
- Quasar
- Nuxt : 
	- Vue 3 
	- Composition API
	- Typescript (Optionel)

Voici certains besoins fonctionnels à respecter :
- [ ] Lors de la mort d'un Agent, tous les Agents sont informés par Message
- [X] Un Agent ne peut pas participer à une Mission s'il n'est pas infiltré dans le Pays de cette dernière 
- [x] Lors du début d'une Mission, un Message est envoyé à tous les Agents de ce Pays sauf ceux qui participent à cette mission
- [ ] Lors de la mort d'un Agent tous ses Messages sont supprimés
- [x] Le niveau de danger d'un Pays dépend du plus haut niveau de danger des Missions actives dans ce pays
- [x] Chaque Agent peut avoir un autre Agent comme mentor 
- [x] Chaque Agent peut infiltrer un seul pays à la fois
- [x] À la fin d'une Mission, un Résultat de mission est créé pour décrire son succès ou son échec
- [x] Lorsque la liste des Agents est récupérée, les noms et prénoms ne sont pas renvoyés 

Pour mieux visualiser leurs opérations les interfaces suivantes sont demandées : 

- [ ] Une interface pour voir la liste des missions et leurs résultats
- [ ] Une interface pour voir les informations d'un agent, ses missions et ses messages
- [ ] Une interface pour créer de nouveaux agents
- [ ] Une interface pour créer de nouvelles missions
- [ ] Une interface pour créer un nouveau message
- [ ] Une interface pour ajouter un nouveau pays
- [ ] Une interface pour clôturer une mission et remplir les informations Résultat de mission

Pour les interfaces utilisez les composants Quasar comme QTable pour la visualisation, QCard, QList, QItem, etc…
Une attention particulière est attribuée à la qualité du code, son organisation et sa clarté.  
Le test doit être exécutable et fonctionnel. Vous n'êtes pas limité en temps et vous n’êtes pas dans l’obligation de terminer tous les points mentionnés.  

Bonne Chance !