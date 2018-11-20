package sequence_match;

//sequence_match.JAVA
//Sequence matching driver
//
//SYNTAX: java Sequence_Match <match length> <corpus file> <pattern file> 
//                [ <mask file> ]
//
//This sequence matching program reads two sequences, a CORPUS and a
//PATTERN, from files on disk and finds all strings of a given match
//length M that occur in both corpus and pattern.  Matching
//substrings are printed in the form 
//   <index 1> <index 2> <substring>
//where the two indices are the offsets of the match within the
//corpus and pattern, respectively, and <substring> is the actual
//matching string.
//
//As an optional fourth argument, the program can take a file
//containing a MASK sequence.  Substrings of the mask sequence are
//considered "uninteresting" and so must not be reported by the
//matching code.  To implement this requirement, we delete any
//occurrences of substrings in the mask sequence from our pattern
//table before performing the search.
//

public class Sequence_Match {
 
 public static void main(String args[]){

	int matchLength   = 0;
	String corpusSeq  = null;
	String patternSeq = null;
	String maskSeq    = null;
	
	if (args.length < 3)
	    {
		System.out.println("Syntax: java Sequence_Match <match length> " +
				   "<corpus file> <pattern file> " +
				   "[<mask file>]");
		return;
	    }
	else
	    {
		matchLength = Integer.parseInt(args[0]);
		corpusSeq   = SeqReader.readSeq(args[1]);
		patternSeq  = SeqReader.readSeq(args[2]);
		
		Terminal.println("M = " + matchLength);
		Terminal.println("CORPUS: " + corpusSeq.length() + " bases");
		Terminal.println("PATTERN: " + patternSeq.length()+" bases");		
		
		
		if (args.length > 3)
		    {
			maskSeq = SeqReader.readSeq(args[3]);
			Terminal.println("MASK: "+maskSeq.length()+" bases");
		    }
	    }
	
	//COMMENT OUT BEFORE SUBMITTING
/*	
	matchLength = 1;
	corpusSeq   = SeqReader.readSeq("test-corpus.txt");
	patternSeq  = SeqReader.readSeq("test-pattern.txt");
	//maskSeq = SeqReader.readSeq("test2-mask.txt");
//*/
	
	
	StringTable table = createTable(patternSeq, matchLength);
	
	if (maskSeq != null)
	    maskTable(table, maskSeq, matchLength);
	
//	table.printTable(); System.out.println("");

	findMatches(table, corpusSeq, matchLength);

	
 }
 
 
 // Create a new StringTable containing all substrings of the pattern
 // sequence.
 // 
 public static StringTable createTable(String patternSeq, int matchLength)
 {
	StringTable table = new StringTable(patternSeq.length());
	

	for (int j = 0; j < patternSeq.length() - matchLength + 1; j++)
	    {
		
		String key = patternSeq.substring(j, j + matchLength);
/*		
		if(j==38397){
			System.out.println("key is "+key);
		}
*/	
		Record rec = table.find(key);
		if (rec == null) // not found -- need new Record
		    {
			rec = new Record(key);
			if (!table.insert(rec))
			    System.out.println("Error (insert): hash table is full!\n");
		    }
		rec.positions.add(new Integer(j));
	    }
	
	return table;
 }
 
 
 // Remove all substrings in the mask sequence from a StringTable.
 // 
   public static void maskTable(StringTable table, String maskSeq,
			  int matchLength)
 {
	for (int j = 0; j < maskSeq.length() - matchLength + 1; j++)
	    {
		String key = maskSeq.substring(j, j + matchLength);
		
		Record rec = table.find(key);
		if (rec != null)
		    table.remove(rec);
	    }
 }
 
 
 // Find and print all matches between the corpus sequence and any
 // string in a StringTable.
 //
 public static void findMatches(StringTable table, String corpusSeq,
				   int matchLength)
 {
	for (int j = 0; j < corpusSeq.length() - matchLength + 1; j++)
	    {
		String key = corpusSeq.substring(j, j + matchLength);

		Record rec = table.find(key);
		if (rec != null)
		    {
			for (int k = 0; k < rec.positions.size(); k++)
			    {
				Terminal.println(j + " " + 
						 rec.positions.get(k) +" "+ 
						 key);
			    }
		    }
/*
		if(j==26669){
			System.out.println("");
			System.out.println("key = "+key+"   StringTable.toHashKey(key) = "+StringTable.toHashKey(key));
			System.out.println("rec.key = "+rec.key+"   StringTable.toHashKey(rec.key) = "+StringTable.toHashKey(rec.key));
			System.out.println("");
		}
		//*/
	    }
 }
}
