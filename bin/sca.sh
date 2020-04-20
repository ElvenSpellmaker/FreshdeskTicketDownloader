path=$(dirname -- $0);

RESULT=0;

function checkForSuccess {
	if [ $? -ne 0 ]; then
		printf "\n\033[0;31mFOUND ERRORS\033[0m\n";
		printf "check the \033[1mbin/sca/$1\033[0m file in the bin/sca/ directory\n\n";
		RESULT=1;
	else
		printf "\n\033[0;32mNO ERRORS\033[0m\n\n";
	fi;
}


printf "\n\033[0;33mSTATIC CODE ANALYSIS\033[0m\n\n";


echo "-------------------------------------------------------------------------------------";

printf "\n\033[0;33mCode Sniffer Analysis started:\033[0m\n";


${path}/sca/phpcs.sh;

checkForSuccess "phpcs.txt";

echo "-------------------------------------------------------------------------------------";

printf "\n\033[0;33mMess Detector Analysis started:\033[0m\n";


${path}/sca/phpmd.sh;

checkForSuccess "phpmd.html";

echo "-------------------------------------------------------------------------------------";

printf "\nDone\n\n";

exit "$RESULT"
