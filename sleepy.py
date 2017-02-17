#!/usr/bin/python
import time
import sys

text_file = open("Output.txt", "w")
text_file.write(str(sys.argv))
text_file.close()

time.sleep(100)

