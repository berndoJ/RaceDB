from dirsync import sync
import sys

if len(sys.argv) >= 2:
    if sys.argv[1] == "test":
        print("Entering test mode...");

        """print("Writing version PHP file...")

        verfile = open("./../website/version/verinfo.txt","r")

        verinfo = verfile.read().splitlines()
        print("Current software version: "+verinfo[0]+"."+verinfo[1]+"."+verinfo[2]+" build "+verinfo[3]+".")
        verfile.close()

        verphpfile = open("./../website/version/verinfo.php", "w+")
        verphpfile.write("<?php\n$VER_MAIN = "+verinfo[0]+";\n$VER_MAJOR = "+verinfo[1]+";\n$VER_MINOR = "+verinfo[2]+";\n$VER_BUILD = "+verinfo[3]+";\n?>");
        verphpfile.close()"""

        if len(sys.argv) >= 3 and sys.argv[2] == "xampp":
            print("Testing code using XAMPP.\nSyncing xampp directory...")
            sync("./../website", "C:/xampp/htdocs/kktdb", "sync", purge = True)
        else:
            print("Test export destination unknown.")
    elif sys.argv[1] == "version":
        if len(sys.argv) >= 3 and sys.argv[2] == "increment":
            print("Incrementing version.")
            verfile = open("./../website/version/verinfo.txt","r")
            verinfo = verfile.read().splitlines()
            verfile.close()
            if len(sys.argv) >= 4 and sys.argv[3] == "main":
                verinfo[0] = int(verinfo[0]) + 1
                verinfo[1] = 0
                verinfo[2] = 0
                verinfo[3] = 0
            elif len(sys.argv) >= 4 and sys.argv[3] == "major":
                verinfo[1] = int(verinfo[1]) + 1
                verinfo[2] = 0
                verinfo[3] = 0
            elif len(sys.argv) >= 4 and sys.argv[3] == "minor":
                verinfo[2] = int(verinfo[2]) + 1
                verinfo[3] = 0
            else:
                verinfo[3] = int(verinfo[3]) + 1
            verfile = open("./../website/version/verinfo.txt","w")
            verfile.write(str(verinfo[0])+"\n"+str(verinfo[1])+"\n"+str(verinfo[2])+"\n"+str(verinfo[3]))
            verfile.close()

            verfile = open("./../website/version/verinfo.txt","r")
            verinfo = verfile.read().splitlines()
            print("Current software version: "+verinfo[0]+"."+verinfo[1]+"."+verinfo[2]+" build "+verinfo[3]+".")
            verfile.close()
        elif len(sys.argv) >= 3 and sys.argv[2] == "reset":
            print("Resetting version.")
            verfile = open("./../website/version/verinfo.txt","w+")
            verfile.write("1\n0\n0\n0")
            verfile.close()
        else:
            verfile = open("./../website/version/verinfo.txt","r")
            verinfo = verfile.read().splitlines()
            print("Current software version: "+verinfo[0]+"."+verinfo[1]+"."+verinfo[2]+" build "+verinfo[3]+".")
            verfile.close()
