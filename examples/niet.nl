hoi

// Logische ontkenning met 'niet' of '!'
stel aangemeld = onwaar;

als (niet aangemeld) {
    zeg "Je bent niet aangemeld.";
}

aangemeld = waar;

als (!aangemeld) {
    zeg "Dit wordt overgeslagen.";
} anders {
    zeg "Welkom terug!";
}

// Gecombineerd
stel a = 5;
stel b = 10;

als (niet (a > b)) {
    zeg "a is niet groter dan b";
}

doei
