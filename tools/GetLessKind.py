import os,shutil,json

DataSetPath="less"

def MakeDir(Path):
    if(not os.path.isdir(Path)):
        os.makedirs(Path)
for O in os.listdir(DataSetPath):
    with open(O+".json","w+") as FB:
        FB.write(json.dumps(os.listdir(DataSetPath+"\\"+O),ensure_ascii=False))