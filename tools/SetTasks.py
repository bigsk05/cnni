import os,shutil,json

PeopleNum=7
DataDir="100\\plant"

def MakeDir(Path):
    if(not os.path.isdir(Path)):
        os.makedirs(Path)
ListDir=os.listdir(DataDir)
if(len(ListDir)%PeopleNum) == 0:
    Single=len(ListDir)//PeopleNum
    Last=Single
else:
    Single=len(ListDir)//PeopleNum
    Last=len(ListDir)%PeopleNum
for i in range(0,PeopleNum-1):
    for r in range(0,Single):
        shutil.move(DataDir+"\\"+os.listdir(DataDir)[0],"output\\"+str(i)+"\\"+os.listdir(DataDir)[0])
for r in range(0,Last):
    shutil.move(DataDir+"\\"+os.listdir(DataDir)[0],"output\\"+str(PeopleNum-1)+"\\"+os.listdir(DataDir)[0])
