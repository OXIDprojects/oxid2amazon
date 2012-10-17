oxid2amazon
===========

OXID eShop CE/PE/EE 4 module for the amazon article export and order import.

Originally registered: 2010-11-23 by anzido on former OXIDforge

--------------------

Voraussetzungen<br>
Voraussetzungen für dieses Modul ist die OXID eShop PE / EE Version 4.2 oder neuer. 

Installation<br>
Legen Sie ein Backup Ihrer Shop-Datenbank sowie Ihrer Shop-Dateien an bzw. vergewissern Sie sich, dass solche Backups vorhanden sind.
Kopieren Sie alle Dateien im Ordner copy_this in Ihr Shopverzeichnis.
Im Verzeichnis changed_full finden Sie einige Templates für den Admin-Bereich. Sollten diese Templates in Ihrem Shop bisher nicht verändert worden sein, so können Sie die den Inhalt des Verzeichnisses ebenfalls einfach in Ihr Shopverzeichnis kopieren.
Falls Sie an den genannten Dateien bereits Änderungen vorgenommen haben, so müssen Sie die in den mitgelieferten Templates deutlich markierten Stellen in Ihrer angepassten Templates übertragen. Falls Ihnen unklar sein sollte, was hier genau zu tun ist, so bitten Sie ggf. einen OXID-Partner um Hilfe.
Führen Sie nun - z. B. mit Hilfe des phpMyAdmins oder einem anderen Datenbank-Tool - das Script install.sql auf Ihrer Shop-Datenbank aus.
Nehmen Sie nun im Admin-Bereich unter anzido Amazon » Destinations im Reiter Destination die Konfiguration des Moduls vor. Sie müssen hier Ihre Amazon Zugangsdaten eintragen sowie die Zugangsdaten zu Ihrem AMTU-Server. 
Für den "Pfad zum Ablage-Verzeichnis" lautet der Wert normalerweise: /production/outgoing.
Für den "Pfad zum Reports-Verzeichnis" lautet der Wert normalerweise: /production/reports.

Sie können an dieser Stelle weitere Einstellungen tätigen bzgl. Sprache, Währung und Logistiker für den aktuellen Amazon-Account.

Im Bereich "Amazon control" haben Sie die Möglichkeit, sämtliche von Ihnen bisher exportierte Artikel bei Amazon zu entfernen.
Im Bereich Cronjobs haben Sie die Möglichkeit, die einzelnen Aktionen des Amazon-Moduls manuell auszulösen. Im Normalfall sollten für diese Aktionen Cronjobs angelegt werden.
Nachdem Sie einen Amazon-Account angelegt haben, können Sie unter dem Reiter Product selector bestimmen, welche Artikel zu Amazon exportiert werden sollen. Sie können dies einerseits auf Basis der Auswahl einer oder mehrerer Kategorien tun und/oder auf Basis von Filterkriterien, die Sie völlig Frei anhand von Feldern der Artikeltabelle festlegen können. Über den Button Beispiel-Produkte können Sie eine Auswahl der Artikel sehen, die auf Basis der von Ihnen eingestellten Kriterien exportiert würde.
Im Menü Settings können und müssen Sie noch einige weitere Einstellungen für Ihren Export vornehmen .... 

##TODO
Leeren Sie ggf. sicherheitshalber das tmp Verzeichnis Ihres Shops.
History ... 