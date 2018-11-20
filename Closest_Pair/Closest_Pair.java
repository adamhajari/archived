//
// Main driver code for closest_pair.
// Updated 9/6/2011
//*
package closest_pair;

import java.util.*;
import java.awt.*;

//=================================== Closest_Pair =======================
//
// Input: n the number of points
//
// Output: A pair of closest points and their distance
//
//========================================================================

public class Closest_Pair {
    
    //================================ main ===============================
    //
    // You can use command line arguments to have the output and/or input
    // come from a file versus the command line.
    //
    // If there are two arguments then the first one is the file from where
    // the input is read and the second is the file where a transcript is
    // saved.
    //
    // If there is just one argument then it is the file where a transcript
    // is saved.
	//
	// A filename argument of the form '@x', where x is a non-negative
	// integer, allocates x random points.  Any other argument is
	// assumed to be a file from which points are read.
	//
	// Note: this program was originally designed to compare the run time between
	// a naive algorithm and a divide and conquer solution.  The Naive 
	// implementation has been commented out.
    
    public static void main(String args[])
    {
	XYPoint points [];
	String fileName = null;
	
	//DELETE THIS LINE WHEN REAL INPUTS ARE PROVIDED
	fileName = "@50";
	
	// If two arguments are given, the first is assumed to
	// be a transcript file, and the second is assumed to
	// be the input spec.  Otherwise, the sole argument is
	// the input spec.
	
	if (args.length >= 2)
	    {
		fileName = args[1];
		Terminal.startTranscript(args[0]);
	    }
	else if (args.length == 1)
	    {
		fileName = args[0];
	    }
	else
	    {
			if (fileName==null){
			Terminal.println("Syntax: Closest_Pair [<transcriptfile>] filename");
			return;
			}
	    }
			
	
	// A filename argument of the form '@x', where x is a non-negative
	// integer, allocates x random points.  Any other argument is
	// assumed to be a file from which points are read.
	
	
	
	if (fileName.charAt(0) != '@')
	    {

		points = PointReader.readXYPoints(fileName);
	    }
	else
	    {
		int nPoints = Integer.parseInt(fileName.substring(1));
		
		
		
		// NB: if you leave the seed argument blank, Java
		// will use a different random seed for each run.
		
		java.util.Random randseq = new java.util.Random(9735);
		
		points = genPointsAtRandom(nPoints, randseq);
	    }
	
	if (points.length < 2)
	    {
		Terminal.println("ERROR: input must contain at least two points");
		return;
	    }
	
	{
	    XComparator lessThanX = new XComparator();
	    YComparator lessThanY = new YComparator();
	    
	    Date startTime = new Date();
	    
	    /////////////////////////////////////////////////////////////////
	    // CLOSEST-PAIR ALGORITHM STARTS HERE	
	    
	    // The algorithm expects two arrays containing the same points.
	    XYPoint pointsByX [] = new XYPoint [points.length];
	    XYPoint pointsByY [] = new XYPoint [points.length];
	    
	    for (int j = 0; j < points.length; j++)
		{
		    pointsByX[j] = points[j];
		    pointsByY[j] = points[j];
		}
	    
	    // Ensure sorting precondition for divide-and-conquer CP
	    // algorithm.  NB: you should *not* have to call sort() in
	    // your own code!
	    
	    Arrays.sort(pointsByX, lessThanX); // sort by x-coord
	    Arrays.sort(pointsByY, lessThanY); // sort by y-coord
	    
	    ClosestPairDC.findClosestPair(pointsByX, pointsByY);
	    
	    // CLOSEST-PAIR ALGORITHM ENDS HERE
	    /////////////////////////////////////////////////////////////////
	    
	    Date endTime = new Date();
	    long elapsedTime = endTime.getTime() - startTime.getTime();
	    
	    //*
	    Terminal.println("For n = " + points.length + 
			     ", the elapsed time is " +
			     elapsedTime + " milliseconds.");
	    Terminal.println("");
	    //*/
	}
	
/*
	// Now run the naive algorithm
	{
	    Date startTime = new Date();
	    
	    ClosestPairNaive.findClosestPair(points);
	    
	    Date endTime = new Date();
	    long elapsedTime = endTime.getTime() - startTime.getTime();
	    
	    
	    Terminal.println("For n = " + points.length + 
			     ", the naive elapsed time is " +
			     elapsedTime + " milliseconds.");
	    Terminal.println("");
	}
	//*/
	// *** NOTE: for your submitted output for Part Two, you MUST print 
	// *** the closest pair of points and the distance between them!
	
	//////////////////////////////////////////////////////////////////////
	// THE FOLLOWING LINES DEMONSTRATE HOW TO USE THE PROVIDED
	// PLOTTER ROUTINE.  Uncomment them as you wish for debugging
	// purposes, or use them in your closest pair code to inspect
	// the point arrays at any time.  For example, you could color
	// all points in the left half red and all points in the right
	// half blue and then visually check that you divided them
	// properly by calling the plotter before you recurse.  Note
	// that if you make several calls, all the plots will
	// initially be on top of each other -- just move them so you
	// can see everything.
	//
	
	// Here the points are plotted and labelled by X-coordinate
	
	// new Plotter(pointsByY, true, "Sorted by X-coordinate");
	
	// Here the points are plotted and labelled by Y-coordinate
	
	//new Plotter(pointsByY, true, "Sorted by Y-coordinate");
	
	// Here's a call to the plot routine in which the points
	// aren't labeled. A nice thing to do at this point (if you
	// computed the two closest points) would be to color the two
	// closest points a different color For a XYPoint p, you could
	// color p (say red) with the line: 
	//
	// p.color = Color.red;
	
	// new Plotter(pointsByX, true, "Output");
    }
    
    
    //
    // genPointsAtRandom()
    // Generate an array of specified size containing
    // points with coordinates chosen at random, using
    // the specified random sequence generator.
    //
    
    static XYPoint[] genPointsAtRandom(int nPoints, 
				       java.util.Random randseq) 
    {
	XYPoint points[] = new XYPoint [nPoints];
	
	double x = 0.0;
	double y = 0.0;
	
	double step = Math.sqrt(nPoints);
	
	for (int j = 0; j < nPoints; j++) 
	    {
		// jitter next point's X coordinate
		x += 2.715 * Math.abs(randseq.nextGaussian());
		
		// move the Y coordinate a random amount up,
		// while keeping it within limits [0 .. nPoints)
		y = (y + step * (randseq.nextDouble() + 1)) % nPoints;
		
		points[j] = new XYPoint((int) Math.round(x), 
					(int) Math.round(y));
	    }
	
	return points;
    }
    
    
    //
    // makeInt()
    // Convert a double between 0 and 1 to an integer between lo and hi
    //
    static int makeInt(double rand, int lo, int hi){   
	return lo + ((int) Math.floor(rand * (hi + 1 - lo)));
    }
}
//*/