import os,shutil

DataSetPath="Desktop"

def MakeDir(Path):
    if(not os.path.isdir(Path)):
        os.makedirs(Path)
def CheckDir(Path):
    if(len(os.listdir(DataSetPath+"\\"+Path))>=500):
        shutil.move(DataSetPath+"\\"+Path,"output\\500")
    else:
        shutil.move(DataSetPath+"\\"+Path,"output\\less")
DirList=os.listdir(DataSetPath)
MakeDir("output\\less")
MakeDir("output\\500")
for O in DirList:
    CheckDir(O)