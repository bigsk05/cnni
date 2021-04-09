import os,requests,threading,shutil

#执行的线程数量
threadNum=5
#储存数据集的路径
dataDir="./data/imagenet/"
#ImageNet的数据集所在的位置
sourcePath="fall.txt"
with open("fall.txt","r") as fb:
    fall=fb.read()
    fallList=fall.split("\n")
    #计算每个线程需要处理的图片数量
    if (len(fallList) % threadNum) == 0:
        numsEach=len(fallList) / threadNum
        numsLast=numsEach
    else:
        numsEach=len(fallList) // threadNum
        numsLast=len(fallList) - (numsEach * (threadNum - 1))
#写入缓存文件
os.makedirs("tmp",exist_ok=True)
for i in range(0,threadNum):
    with open("tmp/"+str(i),"w+") as fb:
        if i == threadNum:
            begin = (i - 1) * numsEach
            end = (i - 1) * numsEach + numsLast
            fb.write(fallList[begin:end])
        else:
            begin = (i - 1) * numsEach
            end = (i * numsEach) - 1
            fb.write(fallList[begin:end])
for i in range(0,threadNum):
    with open("tmp/"+str(i),"r") as fb:
        for line in fb:
            if line:
                fallPart=line.split().split('\t')
                assert len(fallPart) == 2
                synset = fallPart[0][0: fallPart[0].index('_')]
                imgPath = os.path.join(dataDir,'images',synset)
                os.makedirs(imgPath,exist_ok=True)
                imgUrl = fallPart[1]
                imgFile=os.path.join(imgPath,"{}.jpg".format(fallPart[0]))
                try:
                    with open(imgFile,"wb") as f:
                        f.write(requests.get(imgUrl).content())
                except:
                    pass
shutil.rmtree("tmp")