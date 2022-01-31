<?php

$name = 'sirdomin-tools';

$homeDir = str_replace($name, '', __DIR__);

$zshFile = $homeDir . '.zshrc';

$pretty_print = function (string $string, string $type = 'success') {
    if ($type === 'success') {
        echo "\e[0;32;40m" . $string . "\e[0m\n";
    } else if ($type === 'skip') {
        echo "\e[1;34;40m" . $string . "\e[0m\n";
    } else {
        echo "\e[0;31;40m" . $string . "\e[0m\n";
    }
};

$install_aliases = function() use ($homeDir, $zshFile, $pretty_print) {
    $zshContent = file_get_contents($zshFile);

    if (str_contains($zshContent, 'source $HOME/.aliases')) {
        $pretty_print('aliases already added to zsh, skipping.', 'skip');
    } else {
        $zshrc = fopen($zshFile, 'a');
        fwrite($zshrc, 'source $HOME/.aliases' . PHP_EOL);
        fclose($zshrc);
        $pretty_print('aliases has been added to zsh');
    }

    $aliasesFileName = $homeDir . '.aliases';

    if (file_exists($aliasesFileName)) {
        $pretty_print('file .aliases is already installed, removing', 'skip');
        unlink($aliasesFileName);
    }
    
    $myfile = fopen($aliasesFileName, "w");
    fclose($myfile);
    file_put_contents($aliasesFileName, file_get_contents(__DIR__ . '/aliases/.aliases'));
    
    $pretty_print(sprintf('.aliases installed to %s', $aliasesFileName));
};

$install_theme = function() use ($homeDir, $zshFile, $pretty_print) {

    $ohMyZshThemesDir = $homeDir . '.oh-my-zsh/themes';
    if (file_exists($ohMyZshThemesDir)) {
        $ohMyZshCustomThemeFile = $ohMyZshThemesDir . '/agnoster-custom.zsh-theme';
        if (file_exists($ohMyZshCustomThemeFile)) {
            $pretty_print(sprintf('%s already installed, removing', $ohMyZshCustomThemeFile), 'skip');
            unlink($ohMyZshCustomThemeFile);
        }

        $myfile = fopen($ohMyZshCustomThemeFile, 'w');
        fclose($myfile);
        file_put_contents($ohMyZshCustomThemeFile, file_get_contents(__DIR__ . '/theme/agnoster-custom.zsh-theme'));

        $pretty_print(sprintf('%s has been created', $ohMyZshCustomThemeFile));
    }

    $zshContent = file_get_contents($zshFile);
    $replacedTheme = preg_replace('/ZSH_THEME="(.*?)\"/m', 'ZSH_THEME="agnoster-custom"', $zshContent);
    file_put_contents($zshFile, $replacedTheme);

    $pretty_print('zsh theme has been set to agnoster-custom');
};

$replace_default_user = function() use ($homeDir, $zshFile, $pretty_print) {
    $zshContent = file_get_contents($zshFile);

    if (str_contains($zshContent, 'export DEFAULT_USER="$(whoami)"')) {
        $pretty_print('export default user already added to zsh, skipping', 'skip');
    } else {
        $zshrc = fopen($zshFile, 'a');
        fwrite($zshrc, 'export DEFAULT_USER="$(whoami)"' . PHP_EOL);
        fclose($zshrc);
        $pretty_print('export DEFAULT_USER="$(whoami)" added to zsh');
    }

};

if (file_exists($zshFile)) {
    $install_aliases();
    $install_theme();
    $replace_default_user();
    echo PHP_EOL;
    $pretty_print('Installation complete');
} else {
    $pretty_print('ZSH is not installed', 'fail');
}
