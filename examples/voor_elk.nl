hoi

stel namen = ["Alice", "Bob", "Charlie"];

voor elk naam in namen {
    zeg "Hoi, " + naam + "!";
}

// Stop en verder werken ook
stel cijfers = [1, 2, 3, 4, 5, 6];
zeg "--- Oneven cijfers ---";
voor elk getal in cijfers {
    als (getal % 2 == 0) {
        verder;
    }
    zeg getal;
}

// Lijst optellen
stel getallen = [10, 20, 30, 40, 50];
stel som = 0;
voor elk n in getallen {
    som += n;
}
zeg "Som:", som;

// Geneste lus
stel rijen = [[1, 2], [3, 4], [5, 6]];
voor elk rij in rijen {
    zeg rij;
}

doei
