import os
import math
import numpy as np
import tensorflow as tf
import matplotlib.pyplot as plt

#声明用于保存数据的全局变量，如果要自动适应数据集的话，可以修改成字典自动存取
mainFall={}
mainLabelFall={}

def get_files(file_dir, ratio):
    i=0
    for dirs in os.listdir(file_dir):
        mainFall[dirs],mainLabelFall[dirs]=[],[]
        for file in os.listdir("{}/{}".format(file_dir,dirs)):
            mainFall[dirs].append("{}/{}/{}".format(file_dir,dirs,file))
            mainLabelFall[dirs].append(i)
        i+=1
    imageListFall=[]
    for key,value in mainFall.items():
        imageListFall.append(value)
    labelListFall=[]
    for key,value in mainLabelFall.items():
        labelListFall.append(value)
        
    #对生成的图片路径和标签List做打乱处理
    image_list = np.hstack(tuple(imageListFall))
    label_list = np.hstack(tuple(labelListFall))

    #利用shuffle打乱顺序
    temp = np.array([image_list, label_list])
    temp = temp.transpose()
    np.random.shuffle(temp)

    #将所有的img和lab转换成list
    all_image_list = list(temp[:, 0])
    all_label_list = list(temp[:, 1])

    #将所得List分为两部分，一部分用来训练tra，一部分用来测试val，ratio是测试集的比例
    n_sample = len(all_label_list)
    n_val = int(math.ceil(n_sample * ratio))#测试样本数
    n_train = n_sample - n_val#训练样本数

    tra_images = all_image_list[0:n_train]
    tra_labels = all_label_list[0:n_train]
    tra_labels = [int(float(i)) for i in tra_labels]
    val_images = all_image_list[n_train:-1]
    val_labels = all_label_list[n_train:-1]
    val_labels = [int(float(i)) for i in val_labels]

    return tra_images, tra_labels, val_images, val_labels

def get_batch(image, label, image_W, image_H, batch_size, capacity):
    '''
    生成batch
    将上面生成的List传入get_batch() ，转换类型，产生一个输入队列queue，因为img和lab，是分开的，所以使用tf.train.slice_input_producer()，然后用tf.read_file()从队列中读取图像
    @image_W,@image_H 设置好固定的图像高度和宽度
    @batch_size 每个batch要放多少张图片
    @capacity 一个队列最大多少
    '''
    #转换类型
    image = tf.cast(image, tf.string)
    label = tf.cast(label, tf.int32)

    input_queue = tf.train.slice_input_producer([image, label])
    label = input_queue[1]
    image_contents = tf.read_file(input_queue[0])

    #将图像解码，不同类型的图像不能混在一起，要么只用jpeg，要么只用png等
    image = tf.image.decode_jpeg(image_contents, channels=3)

    #数据预处理，对图像进行旋转、缩放、裁剪、归一化等操作，让计算出的模型更健壮
    image = tf.image.resize_image_with_crop_or_pad(image, image_W, image_H)
    image = tf.image.per_image_standardization(image)

    #生成batch
    image_batch, label_batch = tf.train.batch([image, label],
                                              batch_size=batch_size,
                                              num_threads=32,
                                              capacity=capacity)
    #重新排列label，行数为[batch_size]
    label_batch = tf.reshape(label_batch, [batch_size])
    image_batch = tf.cast(image_batch, tf.float32)
    return image_batch, label_batch