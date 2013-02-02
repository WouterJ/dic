# Een bug aanmelden

Mocht je een bug tegenkomen, maar dat niet zelf kunnen oplossen is het handig
als je een issue aanmaakt. Nog beter is het om een failing test te maken die
laat zien wat er fout gaat. Mocht je dat niet kunnen moet je zorgen dat je bug
zo duidelijk mogelijk wordt, vertel bijv. wat je verwachte dat er gebeurd en
wat er echt gebeurde.

# Meehelpen aan WjDic

Ik ben je erg dankbaar als je mee helpt dit project naar een hoger level te
krijgen! Om dit alles goed te laten verlopen moet je wel de normale Git(hub)
gang van zaken volgen. Mocht je hier al eerder mee gewerkt hebben: Ga je gang!
En anders lees je dit even heel snel door.

## 1. Forken

Allereerst moet je deze repo forken. Hoe je dit moet doen lees je [in de guide
van github][1].

## 2. Een nieuwe branch maken

Zodra jou fork lokaal staat moet je een branch maken. Dit doe je met:

````bash
$ git checkout -b jouw_nieuwe_feature
````

Geef je branch een logische naam. Bij voorkeur `add_*` voor een nieuwe feature
of `fix_*` voor een bug/typo fix.

## 3. Aan de slag

Nu kun je in deze branch aan de slag om jou ding te doen. Vergeet niet vaak te
commiten, beter een commit teveel dan 1 te weinig.

### 3.1 Een test maken

WjDic is opgebouwt volgens Test Driven Development. Maak eerst een test aan
voordat je gaat werken aan een feature/bug fix. Een *failing test* zorgt
ervoor dat een probleem beter op te lossen is.

## 4. Pull Request aanvragen

Push jouw branch naar github en [maak een pull request aan][2]. Aarzel niet om
al vast een pull request aan te maken als je nog niet klaar bent. Plaats
gewoon `[WIP]` voor je Pull Request titel. Een Pull Request is een geweldige
manier om je code te laten reviewen en om iedereen bij je contributie te
betrekken, waardoor het nog beter wordt.

 > A Pull Request is a discussion<br>
 > -- [Zach Holman][3]

 [1]: https://help.github.com/articles/fork-a-repo
 [2]: https://help.github.com/articles/using-pull-requests
 [3]: https://speakerdeck.com/holman/how-github-uses-github-to-build-github?slide=31
