## BookCast *(AudioBook and PodCast)*
Simple PHP script that generates a Itunes podcast feed from a set of audios. 

To start the program:
- Create a folder called `books` on the root of the folder
   > ...or elsewhere and bind-mount it to the root foder with the name `books`.
- Copy the folder of your audiobook/podcast inside.
- Copy `feed.php` inside the folder of your book.
- Create a `info.json` file inside the folder of your book.
  ```json
  {
    "title": "The Phantom of the Opera",
    "author": "Gaston Leroux",
    "summary": "The Phantom of the Opera (French: Le Fantôme de l'Opéra) ...",
    "chapters": {
      "FILENAME_FOR_EPISODE_01": "NAME_OF_THE_EPISODE_1",
      "FILENAME_FOR_EPISODE_02": "NAME_OF_THE_EPISODE_2"
    }
  }
  ```
- *[OPTIONAL]* Add a `cover.jpg` file also inside the podcast/audiobook folder.
- You can now go for the root of your site, to see a list o books, or to the specific folder of your book to see the feed.
