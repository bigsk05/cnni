import os,requests

#储存数据集的路径
dataDir="./data/imagenet/"
#ImageNet的数据集所在的位置
sourcePath="fall.txt"

with open("fall.txt","r") as fb:
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
