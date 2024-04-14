use strict;
use common;
use IPC::Run3 'run3';

my $verbose = "debug";


sub commandsExecutor
{
    my ($cmds, $prefix, $subroutine) = @_;
    foreach (@$cmds)
    {
        my $cmd;
        my $result = &common::OK;
        if (!$prefix) {
            $cmd = $_;
        } else {
            $cmd = $prefix . " bash -c \"$_\"";
        }

        my $subroutineStr = $subroutine ? "[${subroutine}]" : "";
        common::dbgLog($verbose, "info", "$subroutineStr $cmd");
        run3 $cmd; $result = ($? >> 8);
        common::dbgLog($verbose, "info", "$subroutineStr $cmd ($?)");

        if ($result) {
            exit;
        }
    }
}

sub help {
    
}


sub build {
    my ($package, $filename, $line, $subroutine) = caller(0);

    my $containerName = "c-sf5";
    my @cmds = ();

    push @cmds, (
        "php -r \\\"copy('https://getcomposer.org/installer', 'composer-setup.php');\\\""
        ,"php -r \\\"if (hash_file('sha384', 'composer-setup.php') == 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;\\\"",
        ,"php composer-setup.php",
        ,"php -r \\\"unlink('composer-setup.php');\\\""
        ,"php composer.phar install"
    );

    commandsExecutor(\@cmds, "docker exec -w /var/www/html $containerName", $subroutine);
}


sub main {
    my $targets = common::getArg('target', \@ARGV, "build");
    my $help = common::getArg('help', \@ARGV);

    my %Targets = (

        "build" => {
            "sub" => \&build,
            "description" => "Builds the application."
        }
    );

    if ($help) {
        help(\%Targets);
        exit common::OK;
    }

    my @targets = split(",", $targets);
    foreach (@targets) {
        my $target = $_;
        my $targetToExecute = $Targets{$target}{"sub"};
        if ($targetToExecute) {
            common::dbgLog($verbose, "info", "Executing target '$target'");
            my $result =  &$targetToExecute($verbose);
            if ($result) {
                common::dbgLog($verbose, "error", "Target $target returned $result");
                return common::FAIL;
            }
        } else {
            common::dbgLog($verbose, "error", "Unknown target: $targetToExecute");
            return common::FAIL;
        }
    }

    return common::OK;

}

main();