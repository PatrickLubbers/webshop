# webshop
webshop

Bijgeleverd is de webshop en een sql export. Deze zit in principe vrij simpel in elkaar. 
Het is een tijdje terug sinds ik hier voor het laatst aan gewerkt heb. 

Het beste is om te beginnen door username.php te openen in localhost en van daaruit te beginnen. Het is mogelijk een inlognaam en wachtwoord in te vullen. Wachtwoord is niet per se nodig. De inputvelden voor registeren en inloggen zijn nu dus in principe dezelfde velden. Het wachtwoord wordt versleutend dmv md5 hashing.

De check of je het juiste wachtwoord hebt ingevuld zit er niet in. Deze had ik in een eerdere versie wel ingebouwd, maar omdat dit niet belangrijk was voor Frank had ik hier toendertijd geen prioriteit aan gegeven. 

De webshop geeft de mogelijkheid tot het toevoegen van items tot je cart. Elke button click voegt het aangeklikte item toe aan je cart. Op het moment is cart als tabel toegevoegd aan de database. 

De volgende tables staan in de database:
cart - columns: ID, user_ID, item_ID
items - columns: ID, item_name, image_url
username - columns: ID, username, password_hashed

Het idee achter een table 'cart' ipv bv op te slaan in een session, was omdat ik makkelijker kon inloggen met verschillende users, en zo eenvoudig kon zien welke items er in de cart van elke user staat. Maar achteraf gezien had ik dit beter eerst in een session kunnen opslaan voordat je deze opgeslagen items in session middels een query insert in de database.






