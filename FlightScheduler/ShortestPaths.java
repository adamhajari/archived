package FlightScheduler;

//
// SHORTESTPATHS.JAVA
// Compute shortest paths in a graph.
//

class ShortestPaths {
    
	private int INF = Integer.MAX_VALUE;
	private PriorityQueue<QElement> q;
	//an array to hold the vertex info so it can be accessed later
	private QElement[] qArray;
	private Multigraph g;
	
    //
    // constructor
    //
    public ShortestPaths(Multigraph G, int startId, 
			 Input input, int startTime) 
    {
    	g = G;
    	InitializeQueue(g, startId);
    	
    	while(!q.isEmpty()){
    		//if the queue is not empty, extract the min element
    		QElement u = q.extractMin();
    		
    		if(u.dist==INF) break;
    		Vertex.EdgeIterator t = u.vert.adj();
    		//if the element is connected to any elements still in the queue
    		//update those adjacent elements with a new distance if decreaseKey returns true
    		while(t.hasNext()){
    			Edge e = t.next();
    			int w = e.weight();
    			QElement v = q.handleGetValue(e.to().handle);

    			if(q.decreaseKey(e.to().handle, u.dist + w)){
    				v.dist = u.dist + w;
    				v.parent = u;
    				v.edge = e;
    			}
    		}
    	}
    }
    
    //
    // returnPath()
    // Return an array containing a list of edge ID's forming
    // a shortest path from the start vertex to the specified
    // end vertex.
    //
    public int [] returnPath(int endId) 
    { 
	
	QElement v = qArray[endId];
	QElement v_end = v;
	
	//count how many vertices are in the path
	int n = 1;
	if(v.parent==null) System.out.println("check2");
	while(v.parent != null){
		v = v.parent;
		n++;		
	}
	
	int path[] = new int[n-1];
	
	//working backward, add each in the path to the array path[]
	v = v_end;
	int i = n-2;
	while(v.parent != null){
		path[i] = v.edge.id();
		v = v.parent;
		i--;		
	}
	
	return path;
    }
    
    private void InitializeQueue(Multigraph G, int startId){
    	//set the start distance to 0 and put it in the queue
    	q = new PriorityQueue<QElement>();
    	qArray = new QElement[G.nVertices()];
    	Vertex vertex = G.get(startId);
    	QElement s = new QElement(vertex);
    	s.dist = 0;
    	
    	qArray[s.vert.id()] = s;
    	vertex.handle = q.insert(0, s);
    	
    	//set all other distances to INF and put them in the queue as well
    	for(int i=0; i<G.nVertices(); i++){
    		if(i!=startId){
    			vertex = G.get(i);
    			QElement u = new QElement(vertex);
    			qArray[u.vert.id()] = u;
    			vertex.handle = q.insert(INF, u);
    		}
    	}
    	
    }
    

}

class QElement{
	/*QElement objects are contain information about the shortest path vertices
	 * including each vertex's parent, the shortest distance to the start vertex,
	 * and the edge length between it and its parent
	 * At initialization, parent is set to null, and dist is set to INF.
	 */
	public Vertex vert;
	public QElement parent;
	public int dist;
	public Handle handle;
	public Edge edge;
	
	public QElement(Vertex v){
		parent = null;
		vert = v;
		dist = Integer.MAX_VALUE;
	}
	
	public String toString()
    {
		/*The string representation for QElements is its vertex's
		 * string representation and its distance from the start vertex
		 */
    	String str = vert.toString() + "," + dist;
		return str;
    }

}
