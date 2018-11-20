package closest_pair;

public class ClosestPairNaive {
    
    public final static double INF = java.lang.Double.POSITIVE_INFINITY;
    
    //
    // findClosestPair()
    //
    // Given a collection of nPoints points, find and ***print***
    //  * the closest pair of points
    //  * the distance between them
    // in the form "(x1, y1) (x2, y2) distance"
    //
    
    // INPUTS:
    //  - points sorted in nondecreasing order by X coordinate
    //  - points sorted in nondecreasing order by Y coordinate
    //
    
    public static void findClosestPair(XYPoint points[]){
    	
    	//for (int i=0;i<10;i++){
    	
    	int nPoints = points.length;
    	double minDist = INF;
    	double dist = INF;
    	int indx1 = 0;
    	int indx2 = 0;
	
    	int j=0;
    	while (j<nPoints-1){
    		int k=j+1;
    		while (k<nPoints){
    			dist = points[j].dist(points[k]);
    			//Terminal.println("dist = " + dist);
    			if (dist < minDist){
    				minDist = dist;
    				indx1 = j;
    				indx2 = k;
    			}
    			k++;
    		}
    		j++;
    	}
    	
    	if (points[indx1].x>points[indx2].x || 
    			(points[indx1].x==points[indx2].x && 
    			points[indx1].y>points[indx2].y)){
    		int tmp = indx1;
    		indx1 = indx2;
    		indx2 = tmp;
    	}
    	
    	//print: (x1,y1) (x2,y2) dist(p1,p2)
    	Terminal.println(""	+ points[indx1] + " " + points[indx2] + " " + minDist);
    	
    	//}
	
    }
}