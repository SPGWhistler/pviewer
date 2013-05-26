#!/bin/bash
i=0;
while read p; do
	echo $p;
	cp images/$p ian/$i.JPG
	i=`expr $i + 1`
done < list.txt
