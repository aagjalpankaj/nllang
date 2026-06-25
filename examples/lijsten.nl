hoi

// Lijst aanmaken
stel getallen = [3, 1, 4, 1, 5, 9, 2, 6];
zeg "Lijst:" , getallen;
zeg "Lengte:", lengte(getallen);
zeg "Eerste:", getallen[0];
zeg "Laatste:", getallen[7];

// Element toevoegen
getallen = duw(getallen, 7);
zeg "Na duw:", getallen;

// Lijst doorlopen
zeg "--- Alle elementen ---";
stel i = 0;
zolang (i < lengte(getallen)) {
    zeg getallen[i];
    i += 1;
}

// Waarden optellen
stel som = 0;
stel j = 0;
zolang (j < lengte(getallen)) {
    som += getallen[j];
    j += 1;
}
zeg "Som:", som;

// Lijst van teksten
stel namen = ["Alice", "Bob", "Charlie"];
stel k = 0;
zolang (k < lengte(namen)) {
    zeg "Hoi, " + namen[k] + "!";
    k += 1;
}

doei
