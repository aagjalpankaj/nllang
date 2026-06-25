<h1 align="center">NLLang</h1>

<p align="center">
  <img alt="CI" src="https://github.com/aagjalpankaj/nllang/actions/workflows/ci.yml/badge.svg"/>
  <img alt="Downloads" src="https://img.shields.io/packagist/dt/aagjalpankaj/nllang"/>
  <img alt="License" src="https://img.shields.io/badge/license-MIT-green"/>
</p>

<p align="center">
  <b>NLLang is een speelse programmeertaal met Nederlandse sleutelwoorden, geschreven in PHP.</b>
</p>

<br>

<blockquote>
<p align="center">
  ⚠️ <b>Waarschuwing:</b> NLLang is gebouwd voor kinderen, nieuwsgierige geesten en mensen die te veel Nederlandse koffie hebben gedronken. Gebruik het <i>niet</i> in productie — tenzij je baas ook Nederlands spreekt en geen idee heeft wat hij doet.
</p>
</blockquote>

<h2 align="center">Installatie</h2>

<h4 align="left">Vereisten: PHP 8.1 of hoger en <a href="https://getcomposer.org">Composer</a>.</h4>

```bash
composer global require aagjalpankaj/nllang
```

> **Eenmalige instelling:** Zorg dat Composer's globale `bin`-map in je `$PATH` staat. Voeg dit toe aan `~/.zshrc` of `~/.bashrc`:
>
> ```bash
> export PATH="$PATH:$(composer global config bin-dir --absolute)"
> ```
>
> Herlaad je shell daarna met `source ~/.zshrc`. Als je dit al eerder hebt gedaan voor een ander globaal Composer-pakket, is deze stap niet nodig.

<h2 align="center">Gebruik</h2>

<h4 align="left">Maak een nieuw bestand aan (<code>test.nl</code>)</h4>

<h4 align="left">Bewerk het bestand in een teksteditor.</h4>

```
hoi
  zeg "Hoi, wereld!";
doei
```

<h4 align="left">Uitvoeren</h4>

```bash
nllang test.nl
```

<h4 align="left">Uitvoer</h4>

```
Hoi, wereld!
```

<h2 align="center">Documentatie</h2>

<h3 align="center">Algemeen</h3>

<p align="center"><code>hoi</code> is het beginpunt van een programma en elk programma moet eindigen met <code>doei</code>. Alles buiten deze blokken wordt genegeerd.</p>

```
Dit wordt genegeerd

hoi
  // Schrijf hier je code
doei

Dit ook
```

<h3 align="center">Variabelen</h3>

<p align="center">Variabelen worden gedeclareerd met <code>stel</code>. Meerdere variabelen kunnen in één statement worden gedeclareerd.</p>

```
hoi
  stel a = 10;
  stel b = "twee";
  stel c = 15;
  stel d, e = 5, f;
  a = a + 1;
  b = 21;
  c *= 2;
doei
```

<h3 align="center">Typen</h3>

<p align="center">Getallen en teksten werken zoals in andere talen. Lege waarden worden aangeduid met <code>niets</code>. <code>waar</code> en <code>onwaar</code> zijn de booleaanse waarden.</p>

```
hoi
  stel a = 10;
  stel b = 10 + (15 * 20);
  stel c = "tekst";
  stel d = 'ook tekst';
  stel e = 3.14;
  stel f = niets;
  stel g = waar;
  stel h = onwaar;
doei
```

<h3 align="center">Ingebouwde functies</h3>

<p align="center">Gebruik <code>zeg</code> om iets af te drukken. Meerdere waarden worden gescheiden door een spatie.</p>

```
hoi
  zeg "Hoi, wereld!";
  stel a = 10;
  {
    stel b = 20;
    zeg a + b;
  }
  zeg 5, 'ok', niets, waar, onwaar;
doei
```

```
Hoi, wereld!
30
5 ok niets waar onwaar
```

<h3 align="center">Bewerkingen</h3>

<p align="center">NLLang ondersteunt de gebruikelijke rekenkundige en vergelijkingsoperatoren. Strings worden samengevoegd met <code>+</code>.</p>

```
hoi
  stel a = 10, b = 3;

  zeg a + b;   // 13
  zeg a - b;   // 7
  zeg a * b;   // 30
  zeg a / b;   // 3.333...
  zeg a % b;   // 1

  zeg a == 10; // waar
  zeg a != b;  // waar
  zeg a > b;   // waar
  zeg a <= 10; // waar

  zeg "Neder" + "land"; // Nederland

  a += 5;
  b *= 2;
  zeg a, b;

  // Logische ontkenning
  zeg niet waar;  // onwaar
  zeg !onwaar;    // waar
doei
```

<h3 align="center">Voorwaarden</h3>

<p align="center">NLLang ondersteunt <code>als</code>/<code>anders als</code>/<code>anders</code> constructies. Het <code>als</code> blok wordt uitgevoerd als de voorwaarde <code>waar</code> is, anders wordt een <code>anders als</code> blok uitgevoerd als de bijbehorende voorwaarde <code>waar</code> is, en het <code>anders</code> blok wordt uitgevoerd als alle voorwaarden <code>onwaar</code> zijn.</p>

```
hoi
  stel score = 75;

  als (score >= 90) {
    zeg "Uitstekend!";
  } anders als (score >= 70) {
    zeg "Goed gedaan!";
  } anders als (score >= 50) {
    zeg "Voldoende.";
  } anders {
    zeg "Onvoldoende.";
  }
doei
```

```
Goed gedaan!
```

<h3 align="center">Lussen</h3>

<p align="center">Statements in een <code>zolang</code> blok worden herhaald zolang de voorwaarde <code>waar</code> is. Gebruik <code>stop</code> om de lus te verlaten en <code>verder</code> om naar de volgende iteratie te gaan.</p>

```
hoi
  stel i = 0;
  zolang (i < 10) {
    i += 1;
    als (i == 5) {
      zeg "Vijf! Doorgaan...";
      verder;
    }
    als (i == 8) {
      zeg "Stop bij acht.";
      stop;
    }
    zeg i;
  }
  zeg "Klaar!";
doei
```

```
1
2
3
4
Vijf! Doorgaan...
6
7
Stop bij acht.
Klaar!
```

<h3 align="center">Functies</h3>

<p align="center">Definieer functies met <code>taak</code> en geef een waarde terug met <code>geef</code>. Functies zijn recursief aanroepbaar.</p>

```
hoi
  taak begroet(naam) {
    zeg "Hoi, " + naam + "!";
  }

  taak faculteit(n) {
    als (n <= 1) {
      geef 1;
    }
    geef n * faculteit(n - 1);
  }

  begroet("wereld");
  zeg faculteit(6);
doei
```

```
Hoi, wereld!
720
```

<h3 align="center">Lijsten</h3>

<p align="center">Maak lijsten aan met <code>[...]</code>. Gebruik de ingebouwde functies <code>lengte</code>, <code>duw</code> en <code>pop</code> om lijsten te bewerken.</p>

```
hoi
  stel getallen = [1, 2, 3];
  zeg lengte(getallen);    // 3
  zeg getallen[0];         // 1

  getallen[1] = 99;
  getallen = duw(getallen, 4);
  zeg getallen;            // [1, 99, 3, 4]

  getallen = pop(getallen);
  zeg getallen;            // [1, 99, 3]
doei
```

<h3 align="center">Voor-elk lus</h3>

<p align="center">Gebruik <code>voor elk</code> om over een lijst te itereren. <code>stop</code> en <code>verder</code> werken ook hier.</p>

```
hoi
  stel namen = ["Alice", "Bob", "Charlie"];

  voor elk naam in namen {
    zeg "Hoi, " + naam + "!";
  }

  // Sla even getallen over
  stel cijfers = [1, 2, 3, 4, 5];
  voor elk n in cijfers {
    als (n % 2 == 0) {
      verder;
    }
    zeg n;
  }
doei
```

```
Hoi, Alice!
Hoi, Bob!
Hoi, Charlie!
1
3
5
```

<h3 align="center">Gebruikersinvoer</h3>

<p align="center">Lees invoer van de gebruiker met <code>vraag()</code>. Gebruik <code>getal()</code> en <code>tekst()</code> om typen om te zetten.</p>

```
hoi
  stel naam = vraag("Wat is je naam? ");
  zeg "Hoi, " + naam + "!";

  stel leeftijd = getal(vraag("Hoe oud ben je? "));
  als (leeftijd >= 18) {
    zeg "Je bent volwassen.";
  } anders {
    zeg "Je bent " + tekst(18 - leeftijd) + " jaar van volwassen.";
  }
doei
```

<h3 align="center">Scoping</h3>

<p align="center">Variabelen leven in het blok waarin ze zijn gedeclareerd. Een binnenste blok kan variabelen van buiten lezen, maar kan ze ook overschaduwen met een nieuwe declaratie.</p>

```
hoi
  stel x = 5;
  {
    stel x = 99;
    zeg x; // 99
  }
  zeg x; // 5
doei
```

<h3 align="center">Commentaar</h3>

<p align="center">Gebruik <code>//</code> voor regelcommentaar en <code>/* */</code> voor blokcommentaar.</p>

```
hoi
  // Dit is een regelcommentaar
  stel a = 10; // ook hier

  /*
    Dit is
    blokcommentaar
  */
  zeg a;
doei
```

<h2 align="center">Sleutelwoordenlijst</h2>

<p align="center">

| NLLang | Betekenis |
|--------|-----------|
| `hoi` | begin van het programma |
| `doei` | einde van het programma |
| `stel` | variabele declareren |
| `zeg` | afdrukken naar de console |
| `als` | als-voorwaarde |
| `anders als` | anders-als-voorwaarde |
| `anders` | anders-blok |
| `zolang` | zolang-lus |
| `stop` | verlaat de lus |
| `verder` | volgende iteratie |
| `voor elk ... in` | itereer over een lijst |
| `taak` | functie definiëren |
| `geef` | waarde teruggeven uit functie |
| `niet` / `!` | logische ontkenning |
| `waar` | booleaanse waarde waar (true) |
| `onwaar` | booleaanse waarde onwaar (false) |
| `niets` | lege waarde (null) |

</p>

<h2 align="center">Ingebouwde functies</h2>

<p align="center">

| Functie | Beschrijving |
|---------|-------------|
| `zeg(...)` | Afdrukken naar de console |
| `vraag(prompt)` | Lees een regel invoer van de gebruiker |
| `lengte(lijst)` | Geef de lengte van een lijst |
| `duw(lijst, waarde)` | Voeg een waarde toe aan het einde van een lijst |
| `pop(lijst)` | Verwijder het laatste element van een lijst |
| `tekst(waarde)` | Zet een waarde om naar tekst |
| `getal(waarde)` | Zet een waarde om naar een getal |

</p>

<h2 align="center">Voorbeelden</h2>

Bekijk de map [`examples/`](examples/) voor kant-en-klare voorbeeldprogramma's:

- [`hallo.nl`](examples/hallo.nl) — Hallo wereld
- [`rekenen.nl`](examples/rekenen.nl) — Rekenkundige bewerkingen
- [`keuze.nl`](examples/keuze.nl) — Voorwaarden en scoping
- [`teller.nl`](examples/teller.nl) — Lussen en Fibonacci
- [`voor_elk.nl`](examples/voor_elk.nl) — Voor-elk lus
- [`functies.nl`](examples/functies.nl) — Functies en recursie
- [`lijsten.nl`](examples/lijsten.nl) — Lijsten en ingebouwde functies
- [`niet.nl`](examples/niet.nl) — Logische ontkenning
