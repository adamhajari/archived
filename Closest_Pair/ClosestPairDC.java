package closest_pair;

public class ClosestPairDC {
    
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
    
    public static void findClosestPair(XYPoint pointsByX[], XYPoint pointsByY[]){
    	
    	//for (int i=0;i<10;i++){
    		
    	int nPoints = pointsByX.length;
    	
    	XYPoint minPoints [] = null;
    	minPoints = new XYPoint [2];
    	minPoints = closestPair(pointsByX, pointsByY, nPoints);
    	
    	//order the points so the left most point is first
    	if (minPoints[0].x>minPoints[1].x || 
    			(minPoints[0].x==minPoints[1].x && 
    					minPoints[0].y>minPoints[1].y)){
    		XYPoint tmp = minPoints[0];
    		minPoints[0] = minPoints[1];
    		minPoints[1] = tmp;
    	}
    	
    	double minDist = minPoints[0].dist(minPoints[1]);
    	//print: (x1,y1) (x2,y2) dist(p1,p2)
    	Terminal.println(""	+ minPoints[0] + " " + minPoints[1] + " " + minDist);
    	
    	//}
    }

    
    
    public static XYPoint [] closestPair(XYPoint pointsByX[], 
		       XYPoint pointsByY[], int nPoints){

    	XYPoint minPoints [] = null;
    	XYPoint minLPoints [] = null;
    	XYPoint minRPoints [] = null;
    	XYPoint minCombPoints [] = null;
    	XYPoint lrPoints [] = null;
    	minPoints = new XYPoint [2];
    	minLPoints = new XYPoint [2];
    	minRPoints = new XYPoint [2];
    	minCombPoints = new XYPoint [2];
    	lrPoints = new XYPoint [2];
    	
    	XYPoint XL [] = null;
    	XYPoint XR [] = null;
    	XYPoint YL [] = null;
    	XYPoint YR [] = null;

    	XL = new XYPoint [nPoints];
    	
    	if (nPoints ==1){
    		minPoints[0] = pointsByX[0];
    		minPoints[1] = new XYPoint((int) INF,(int) INF);
    		return minPoints;
    	}
    	if (nPoints == 2){
    		minPoints[0] = pointsByX[0];
    		minPoints[1] = pointsByX[1];
    		return minPoints;
    	}else if (nPoints > 2){
    		int midPoint = (int) (Math.ceil((nPoints-1)/2.0));
    		XL = new XYPoint [nPoints];
    		XR = new XYPoint [nPoints];
    		YL = new XYPoint [nPoints];
    		YR = new XYPoint [nPoints];
    		int YLindx = 0;
    		int YRindx = 0;
    		for (int i=0; i<midPoint; i++){
    			XL[i] = pointsByX[i]; 
    		}
    		for (int i=midPoint; i<nPoints; i++){
    			XR[i-(midPoint)] = pointsByX[i];
    		}
    		
    		for (int i=0; i<nPoints; i++){
    			//if (pointsByY[i].x < pointsByX[midPoint].x){
    			if (pointsByY[i].isLeftOf(pointsByX[midPoint])){
    				YL[YLindx] = pointsByY[i];
    				YLindx++;
    			}else{
    				YR[YRindx] = pointsByY[i];
    				YRindx++;
    			}   			
    		}
    		int LnPoints = YLindx;
    		int RnPoints = YRindx;
    		
    		minLPoints = closestPair(XL, YL, LnPoints);
    		minRPoints = closestPair(XR, YR, RnPoints);
    		double distL = minLPoints[0].dist(minLPoints[1]);
    		double distR = minRPoints[0].dist(minRPoints[1]);
    		
    		//lrPoints is the closer of the 2 pairs of points minLPoints and minRPoints
    		if (distL <= distR){
    			lrPoints = minLPoints;
    		}else{
    			lrPoints = minRPoints;
    		}
    		
    		minCombPoints = Combine(pointsByY, pointsByX[midPoint],nPoints, lrPoints);
    		
    		return minCombPoints;
    	}
    	return minCombPoints;
    }
    
  
    static XYPoint [] Combine(XYPoint pointsByY[], 
		       XYPoint midPoint, int nPoints, XYPoint lrPoints[]) {
    	
    	double lrDist = lrPoints[0].dist(lrPoints[1]);
    	XYPoint minCombPoints [] = lrPoints;
    	
    	XYPoint yStrip [] = new XYPoint [nPoints];
    	int yStripIndx = 0;
    	double dist = INF;
    	
    	//construct yStrip
    	for (int i=0; i<nPoints; i++){
    		if (Math.abs(pointsByY[i].x-midPoint.x)<lrDist){
    			yStrip[yStripIndx] = pointsByY[i];
    			yStripIndx++;
    		}
    	} 
    	int yStripLength = yStripIndx;
    	double minDist = lrDist;
    	
    	for (int j=0; j<yStripLength-1; j++){
    		int k = j+1;
    		while (k<yStripLength && yStrip[k].y-yStrip[j].y<lrDist){
    			dist = yStrip[j].dist(yStrip[k]);
    			
    			if (dist < minDist){
    				minCombPoints[0] = yStrip[j];
    				minCombPoints[1] = yStrip[k];
    				minDist = dist;
    			}
    			k++;
    		}
    	}
    	
    	return minCombPoints;
    }

}
