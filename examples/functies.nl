hoi

// Functiedefinitie
taak begroet(naam) {
    zeg "Hoi, " + naam + "!";
}

// Functie met terugkeerwaarde
taak kwadraat(n) {
    geef n * n;
}

// Recursie: faculteit
taak faculteit(n) {
    als (n <= 1) {
        geef 1;
    }
    geef n * faculteit(n - 1);
}

// Fibonacci
taak fibonacci(n) {
    als (n <= 1) {
        geef n;
    }
    geef fibonacci(n - 1) + fibonacci(n - 2);
}

begroet("wereld");
zeg kwadraat(7);

zeg "--- Faculteit ---";
stel i = 1;
zolang (i <= 8) {
    zeg tekst(i) + "! = " + tekst(faculteit(i));
    i += 1;
}

zeg "--- Fibonacci ---";
stel j = 0;
zolang (j < 10) {
    zeg fibonacci(j);
    j += 1;
}

doei
