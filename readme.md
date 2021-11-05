## BookCast *(AudioBook and PodCast)*
Simple PHP script that generates a Itunes podcast feed from a set of audios. 

To start the program:
- Create a folder called `podcasts` on the root of the folder
   > ...or elsewhere and bindmount it to the root foder with the name `podcast`.
- Copy the folder of your audiobook/podcast inside.
- Create a `info.json` file inside the folder.
  ```json
  {
    "title": "The Phantom of the Opera",
    "author": "Gaston Leroux",
    "summary": "The Phantom of the Opera (French: Le Fantôme de l'Opéra) is a novel by French author Gaston Leroux. It was first published as a serial in Le Gaulois from 23 September 1909 to 8 January 1910, and was released in volume form in late March 1910 by Pierre Lafitte. The novel is partly inspired by historical events at the Paris Opera during the nineteenth century, and by an apocryphal tale concerning the use of a former ballet pupil's skeleton in Carl Maria von Weber's 1841 production of Der Freischütz. It has been successfully adapted into various stage and film adaptations, most notable of which are the 1925 film depiction featuring Lon Chaney, and Andrew Lloyd Webber's 1986 musical."
  }
  ```
- *[OPTIONAL]* Add a `cover.jpg` file also inside the podcast/audiobook folder.
- *[OPTIONAL]* Set a password:
  - Rename `.env.dist` onto `.env` and set the password inside.
- Run `docker-compose up`
