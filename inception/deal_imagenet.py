import os,tarfile

label="imageNetLabel.txt"
setAddr="ILSVRC2012_img_train/"
save="dataset/"

def untar(fname, dirs): 
    tar=tarfile.open(fname)
    tar.extractall(path = dirs) 

if __name__ == '__main__':
    os.makedirs("tmp",exist_ok=True)
    with open(label,"r") as fb:
        for line in fb:
            fall=line.split(" ",1)
            if os.path.isfile("{}{}.tar".format(setAddr,fall[0])):
                print(True)
                print("{}.tar".format(fall[0]))
                print("{}".format(fall[1]))
                untar("{}{}.tar".format(setAddr,fall[0]),"untar/{}".format(fall[0]))
