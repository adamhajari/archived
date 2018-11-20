//lowClub -- simulates the deal of all clubs and first play of a lowest clubs spades game
/*One variation of the card game Spades dictates that each round begin by each
 * player laying down his or her lowest club.  The player with the highest lowest club
 * thus automatically wins the first trick.  The probability of a particular club
 * winning the first trick is an important consideration in bidding, particularly
 * when a nil bid is being considered.  lowest_club simulates the deal and first
 * trick of a game of spades under these constraints.  From this simulation, the
 * experimental probability of each club winning the first trick is calculated
*/

#include <iostream>
#include <math.h>
#include <stdlib.h>
#include <time.h>
#include <fstream>
#include <iomanip>
#include <sstream>
#include <string>
using namespace std;

int rand_1to52();

int main()
{

	srand(time(NULL)); //Set a seed for rand-num gen.


	double wins[13] = {0};		//win keeps track of how many times each club was the highest lowest club
	double played[13] = {0};	//play keeps track of how many times each club was played
	double percentWin[13] = {0.0};
	int max, temp, randPick,indx;
	int deck[52] = {0};
	double n = 10000000; //number of hands to simulate

	ofstream output("lowest_club_results.txt"); //Open disk file output.txt
	output <<"Lowest Spades Simulated Results"<< endl;
	output <<"n = "<< n << endl<< endl;
	output << "card " << " wins " << " played " << " %wins" << endl;

	//deal n hands
	for(double j=0; j<n; j++){


		//"shuffle" deck
		//note: deck[j] < 13 but >0 is a club.  deck[j]=0 means the card at index j
		//would have been something besides a club.  In this way we only need to deal
		//out the clubs rather than the whole deck
		for(int i=0; i<13; i++){
			randPick = rand_1to52();
			while(deck[randPick] > 0){ //if deck[randPick] has already been set (is not zero)
				randPick = rand_1to52(); //pick a different index
			}
			deck[randPick] = i+1; //give the index randPick the next club
		}

		//lowestClubs[j] is the lowest club held by player j
		//lowClubs will take values from 1(two) to 13(Ace)
		int lowestClubs[4] = {14,14,14,14};

		for(int i=0; i<4; i++){
			for(int k=0; k<13; k++){
				indx = i*13+k;
				//the first 13 cards are dealt to player j=1
				//the next 13 cards are dealt to player j=2 and so on.
				//we only need to keep track of the lowest club each player receives
				//so lowestClubs[j] only updates when a lower club is dealt to player j
				if(deck[indx]>0){
					if(deck[indx]<lowestClubs[i]){
						lowestClubs[i] = deck[indx];
					}
					deck[indx]=0; //ensure that deck is reset by next deal
				}
			}
		}
		//if lowestClubs[j]=14 at this point, it means player j was dealt no clubs

		//now find the highest lowest club
		max = 0;
		for(int i=0; i<4; i++){
			if (lowestClubs[i] > 0 && lowestClubs[i]<14){
				//increment played[j] when when the j+1 of clubs is played
				played[lowestClubs[i]-1] = played[lowestClubs[i]-1]+1;
				if (max < lowestClubs[i]-1){
					max = lowestClubs[i]-1; //max+1 is the highest lowest club
				}
			}
		}

		wins[max] = wins[max] + 1; //increment wins[i] max where i=max
		if(max == 0){
			//having 2 of clubs as the highest lowest club is a rare occurrence.
			//print it to the screen if it happens and have a glass of champagne
			cout << lowestClubs[0] << "  " << lowestClubs[1] << "  " << lowestClubs[2] << "  " << lowestClubs[3] << "  " << endl;
		}

	}

	output.precision(2);	output.setf(ios::fixed);

	//print the results to the output file
	for(int i=0; i<13; i++){
		if (played[i] > 0){
		percentWin[i] = 100*double(wins[i])/double(played[i]);
		}

		if (i+2 < 10){
			output << "  " << i+2 << "   " << wins[i] <<  "   " << played[i] <<  "   " << percentWin[i] << endl;
			cout << "  " << i+2 << "   " << wins[i] <<  "   " << played[i] <<  "   " << percentWin[i] << endl;
		}
		else{
			output << "  " << i+2 << "  " << wins[i] <<  "   " << played[i] <<  "   " << percentWin[i] << endl;
			cout << "  " << i+2 << "  " << wins[i] <<  "   " << played[i] <<  "   " << percentWin[i] << endl;
		}
	}


	output.close();

	cout <<"Thank You!" << endl;
}
int rand_1to52() {

    int out;
	double randnum;

	randnum = double(rand());
	randnum = 53*randnum/double(RAND_MAX);
	out = int(randnum);

	return out;
}
