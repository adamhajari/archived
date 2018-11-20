package FlightScheduler;

//
// PRIORITYQUEUE.JAVA
// A priority queue class supporting sundry operations needed for
// Dijkstra's algorithm.
//

class PriorityQueue<T> {
	
	//Record holds a key and value for each record in the queue
	public Record<T>[] Q;
	
	//size is the size of the queue (including the null element at Q[0]
	private int size;
    
    // constructor
    //
    public PriorityQueue()
    {
    	//create an array of size 1
    	Q = new Record[1];
    	size = 1;
    }
    
    // Return true iff the queue is empty.
    //
    public boolean isEmpty()
    {
    	//if size is 1, the array contains only the null element at Q[0]
    	if (size==1) return true;
    	else return false;
    }
    
    // Insert a pair (key, value) into the queue, and return
    // a Handle to this pair so that we can find it later in
    // constant time.
    //
    Handle insert(int key, T value)
    {
    	//if Q is full, double its size
    	if (Q.length == size){
    		Record<T>[] temp = Q;
    		Q = new Record[size*2];
    		for(int j=0; j<size; j++){
    			Q[j] = temp[j];
    		}
    	}

    	//Insertion starts at the bottom of the heap and works its way up, 
    	//trying to find the right place to insert the new key k. 
    	int i = size;
    	Handle h = new Handle(i);
    	Q[i] = new Record<T>(key, value, h);
    	int halfIndx = (int)Math.floor(i/2);
    	if (halfIndx <=1 ) halfIndx = 1;
    	//If the key Q[i] is smaller than the new key k, move Q[i] below k.
    	while (i > 1 && key < Q[halfIndx].key){
    		swap(i, halfIndx);
    		i = (int)Math.floor(i/2);
    		halfIndx = (int)Math.floor(i/2);
    		if (halfIndx <=1 ) halfIndx = 1;
    	}
    	
    	//the queue is one element larger so increase its size
    	size++;
    	
    	return h;
    }
    
    // Return the smallest key in the queue.
    //
    public int min()
    {	
    	//Q[1] will always have the smallest key
    	if(isEmpty()) return 0;
    	else return Q[1].key;
    }
    
    // Extract the (key, value) pair associated with the smallest
    // key in the queue and return its "value" object.
    //
    public T extractMin()
    {
    	if (!isEmpty()){
    		//Q[1] has the smallest key
	    	T value = Q[1].value;
	    	//since you're removing it set its handle to point to the null element
	    	Q[1].handle.indx = 0;
	    	
	    	//take the last element in the queue, put it at the top, and call heapify starting at top
	    	Q[1] = Q[size-1];
	    	Q[1].handle.indx = 1;
	    	size--;
	    	heapify(1);
	    	return value;
    	}else return null;
    }
    
    private void heapify(int i){
    	//if i is not a leaf
    	if (i <= (int)Math.floor(size/2)){
    		//find the child of Q[i] with the smallest key
    		int j;
    		if (size<2*i+1 || Q[2*i].key < Q[2*i+1].key){
    			j = 2*i;
    		}
    		else{
    			j = 2*i+1;
    		}
    		//if Q[i].key is larger than either of its childs' keys swap Q[i] 
    		//with its smallest child and heapify from that point
    		if (Q[j].key < Q[i].key){
    			swap(i, j);
    			heapify(j);
    		}
    	}
    }
    
    
    // Look at the (key, value) pair referenced by Handle h.
    // If that pair is no longer in the queue, or its key
    // is <= newkey, do nothing and return false.  Otherwise,
    // replace "key" by "newkey", fixup the queue, and return
    // true.
    //
    public boolean decreaseKey(Handle h, int newkey)
    {
    	//check that h doesn't refer to a removed key
    	
    	if (Q[h.indx]==null) return false;
    	if (h.indx > size) return false;
    	//check that the newkey is smaller than the old key
    	if (newkey > Q[h.indx].key) return false;
    	
    	//decrease works like insert key except rather than starting at the 
    	//bottom of the queue, it start at h's index
    	int i = h.indx;
    	Q[i].key = newkey;
    	
    	int halfIndx = (int)Math.floor(i/2);    	
    	
    	while (i > 1 && newkey<Q[halfIndx].key){
    		swap(i, halfIndx);
    		i = (int)Math.floor(i/2);
    		halfIndx = (int)Math.floor(i/2);
    		if (halfIndx <=1 ) halfIndx = 1;
    	}
    	
    	h.indx = i;
    	
    	if (newkey == Q[h.indx].key) return true;
    	return false;
    }
    
    
    // Get the key of the (key, value) pair associated with a 
    // given Handle. (This result is undefined if the handle no longer
    // refers to a pair in the queue.)
    //
    public int handleGetKey(Handle h)
    {
    	return Q[h.indx].key;
    }

    // Get the value object of the (key, value) pair associated with a 
    // given Handle. (This result is undefined if the handle no longer
    // refers to a pair in the queue.)
    //
    public T handleGetValue(Handle h)
    {	
    	if(Q[h.indx]== null) return null;
    	return Q[h.indx].value;
    }
    
    // Print every element of the queue in the order in which it appears
    // in the implementation (i.e. the array representing the heap).
    public String toString()
    {
    	String str = "";
    	for (int i=1; i < size; i++){
    		str = str + " " + Q[i].value;
    	}
	return str;
    }
    
    //the swap method swaps the two queue elements at the given indices and updates their handles
    private void swap(int i1, int i2){
    	Record<T> temp = Q[i1];
    	Q[i1] = Q[i2];
    	Q[i2] = temp;
    	Q[i1].handle.indx = i1;
    	Q[i2].handle.indx = i2;
    }
}

//the queue holds objects of type Record
//these records hold a key and a value
class Record<T> {
	public int key;   // Link to next element in the list.
	public T value;  // Link to the element in the same pillar directly below
	public Handle handle;
	public Record(int k, T val, Handle h){
		key = k;
		value = val;
		handle = h;
	}
}