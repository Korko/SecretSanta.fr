<?php

return [
    'nav' => [
        'go' => 'Los geht\'s!',
        'contact' => 'Ich habe noch eine Frage',
    ],

    'categories' => [
        'general' => 'Allgemein',
        'technical' => 'Technisch',
    ],

    'questions' => [
        'general' => [
            'Warum wurde SecretSanta.fr entwickelt?' => 'Der Entwickler organisierte regelmäßig Secret Santa-Abende mit Freunden oder Kollegen, bei denen jeder seinen Namen in einen Hut warf. Jeder zog ein Zettel und es kam mehrmals vor, dass jemand seinen eigenen Namen zog. Manchmal wurde die Ziehung wiederholt, manchmal sagte die Person nichts, und der Entwickler fand das schade. Daher kam ihm die Idee, ein Tool zu entwickeln. Das Ziel war es, alles automatisch zu machen, damit niemand seinen eigenen Namen zieht. Selbst der Organisator nahm wie alle anderen teil, da er nicht wissen konnte, wer wen gezogen hatte. Später kamen Ausschlüsse und andere Funktionen dazu.',
            'Wie kann diese Seite kostenlos betrieben werden?' => 'SecretSanta.fr ist für die Nutzer vollkommen kostenlos, nicht aber für den Entwickler, der die verschiedenen Kosten trägt. Es gibt keine Werbung und keine Weitergabe von Informationen.',
            'Wir sind eine ungerade Zahl an Teilnehmern, ist das ein Problem?' => 'Es gibt absolut kein Problem, ob Sie eine gerade oder ungerade Zahl an Teilnehmern haben. Ein Beispiel mit 3 Teilnehmern: Anaïs, Béatrice und Cédric, könnte Anaïs an Béatrice verschenken, Béatrice an Cédric und Cédric an Anaïs. Jeder hat ein Geschenk und niemand zieht sich selbst.',
            'Ich habe mich bei meiner E-Mail-Adresse vertan, als ich mein Secret Santa organisiert habe, was kann ich tun?' => 'Um dieses Problem zu beheben, können Sie entweder eine neue Ziehung organisieren oder einen Teilnehmer bitten, Ihnen den Link zu schicken, der es ihm ermöglicht, seinem/ihrem Secret Santa zu schreiben. Gehen Sie dann auf die Seite https://secretsanta.fr/fix. Wenn Sie es dennoch nicht ändern können, senden Sie den Link des Teilnehmers per E-Mail an: help@secretsanta.fr. Nach Überprüfung und in seiner Freizeit kann der Entwickler Ihnen möglicherweise weiterhelfen.',
            'Ich habe meine Zugangsmail zum Organisatorbereich gelöscht, was kann ich tun?' => 'Um dieses Problem zu beheben, können Sie entweder eine neue Ziehung organisieren oder einen Teilnehmer bitten, Ihnen den Link zu schicken, der es ihm ermöglicht, seinem/ihrem Secret Santa zu schreiben, und dann auf die Seite https://secretsanta.fr/fix gehen.',
            'Ich habe mich bei der Adresse eines Teilnehmers vertan' => 'Wenn Sie Ihr Secret Santa organisiert haben, sollten Sie eine E-Mail mit einem Link zum Organisatorbereich erhalten haben. Von dort aus können Sie die E-Mail-Adresse jedes Teilnehmers ändern.',
            'Ein Teilnehmer hat die E-Mail nicht erhalten, was tun?' => 'Auch wenn die angegebene Adresse korrekt ist, kann es passieren, dass die E-Mail verloren geht, im Spam-Ordner landet oder ein anderes Problem auftritt. Im Organisatorbereich können Sie einen Button neben dem Status der E-Mail-Zustellung finden, um die E-Mail erneut zu senden. So kann der Teilnehmer erfahren, wer sein Ziel ist und wie er mit seinem Santa kommuniziert.',
            'Ist es möglich, mit seinem Ziel zu sprechen?' => 'Momentan ist das nicht möglich, aus Gründen der Anonymität. Nur das Ziel kann seinem/ihrem Secret Santa schreiben. Aber das ist in Planung.',
            // "In meinem Organizer/Organizer-Panel gehen einige E-Mails nicht in den Status ‚Empfangen‘ über. Funktioniert etwas nicht?“
            // "In meinem Organizer/Organizer-Panel wechseln manche E-Mails nicht in den Status ‚Gelesen‘, obwohl die Person sie gelesen hat. Ist das ein Fehler?“
            'Wann werden meine persönlichen Daten gelöscht?' => 'Alle Ihre Daten aus einer Ziehung werden 7 Tage nach Ablauf des Ereignisses gelöscht. Diese Frist wurde festgelegt, um dem Organisator die Liste der Teilnehmer mit ihren gezogenen Zielen zuzusenden, um bei einer späteren Secret Santa-Veranstaltung mit denselben Personen zu helfen und zu verhindern, dass dieselben Ziele wiederholt werden.',
            'Ich habe einen Teilnehmer vergessen, wie kann ich ihn hinzufügen?' => 'Leider erlaubt die Gestaltung von SecretSanta.fr es nicht, jemanden nach der Ziehung hinzuzufügen. Eine Möglichkeit bleibt jedoch, außer die Ziehung zu wiederholen: Sie können dieser Person Ihr eigenes Ziel zuweisen und selbst ein Geschenk für diesen neuen Teilnehmer kaufen.',
            'Wer kann die Liste der Ziele einsehen?' => 'Kurz gesagt: Niemand. Lang gesagt: Nur der Entwickler kann diese Information mit dem Link des Organisators einsehen. Aber es ist besser, dieses Geheimnis so gut wie möglich zu wahren.',
        ],

        'technical' => [
            'Welche Daten werden gespeichert und warum?' => "Für jeden Teilnehmer werden der Name und die E-Mail-Adresse gespeichert, sowie für jede Organisation der Titel und Inhalt der gesendeten E-Mail und jede Nachricht, die zwischen den Teilnehmern über den per E-Mail erhaltenen Link (genannt 'Lieber Weihnachtsmann') gesendet wurde. Sie werden aus zwei Gründen gespeichert: Erstens, um diese Funktion zu ermöglichen, mit der man seinem/ihrem Secret Santa schreiben kann. Zweitens, um E-Mails im Falle eines Adressfehlers erneut zu versenden.",
            'Wie werden die Daten gespeichert?' => 'Jedes Element wird mit AES-256 verschlüsselt und erhält einen einzigartigen Schlüssel pro Organisation. Dieser Schlüssel wird nicht gespeichert und wird an jeden Teilnehmer gesendet. Der Administrator kann also niemals auf die Daten zugreifen, ohne dass Sie aktiv handeln. Jedes Mal, wenn Sie eine Aktion durchführen, verwenden Sie automatisch diesen Schlüssel, der nur für die angeforderte Aktion an SecretSanta überlassen wird, ohne dass er gespeichert wird.',
            'Ich möchte meine Daten löschen, wie kann ich das tun?' => 'Wegen der Art, wie die Daten gespeichert sind, kann der Administrator nicht wissen, welche Daten zu wem gehören. Nur der Organisator kann die Daten aller Teilnehmer auf einmal löschen. Andernfalls werden diese Daten automatisch kurz nach dem Ende der Veranstaltung gelöscht.',
            'Ich möchte den Quellcode selbst überprüfen, wo finde ich ihn?' => 'Sehr gerne! Der Quellcode ist unter folgender Adresse zu finden: https://framagit.org/Korko/SecretSanta. Den Link finden Sie auch oben rechts auf der Hauptseite im kleinen roten Banner.',
        ],
    ],
];
