## BookCast *(AudioBook and PodCast)*
Simple PHP script that generates a Itunes podcast feed from a set of audios. 

To start the program:
- Create a folder called `podcasts` on the root of the folder
   > ...or elsewhere and bind-mount it to the root foder with the name `podcast`.
- Copy the folder of your audiobook/podcast inside.
- Create a `info.json` file inside the folder.
  ```json
  {
    "title": "The Phantom of the Opera",
    "author": "Gaston Leroux",
    "summary": "The Phantom of the Opera (French: Le Fantôme de l'Opéra) ..."
  }
  ```
- *[OPTIONAL]* Add a `cover.jpg` file also inside the podcast/audiobook folder.
- *[OPTIONAL]* Set a password:
  - Rename `.env.dist` onto `.env` and set the password inside.
- Run `docker-compose up`
