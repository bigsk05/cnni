import os
import numpy as np
import tensorflow as tf
import input_data
import inceptionV1

#变量声明
N_CLASSES=4#种类数量
IMG_W=64#Resize大小，否则对内存要求很大
IMG_H=64
BATCH_SIZE=20
CAPACITY=200
MAX_STEP=2000#最大步数，根据实际训练集大小调整
learning_rate=0.0001#一般小于0.0001

train_dir='dataset/'#样本读取路径
logs_train_dir='save/'#模型保存路径

train, train_label, val, val_label=input_data.get_files(train_dir, 0.3)
train_batch, train_label_batch=input_data.get_batch(train, train_label, IMG_W, IMG_H, BATCH_SIZE, CAPACITY)
val_batch, val_label_batch=input_data.get_batch(val, val_label, IMG_W, IMG_H, BATCH_SIZE, CAPACITY)

train_logits=inceptionV1.inference(train_batch, BATCH_SIZE, N_CLASSES)
train_loss=inceptionV1.losses(train_logits, train_label_batch)
train_op=inceptionV1.trainning(train_loss, learning_rate)
train_acc=inceptionV1.evaluation(train_logits, train_label_batch)

test_logits=inceptionV1.inference(val_batch, BATCH_SIZE, N_CLASSES)
test_loss=inceptionV1.losses(test_logits, val_label_batch)
test_acc=inceptionV1.evaluation(test_logits, val_label_batch)

summary_op=tf.summary.merge_all()

sess=tf.Session()
train_writer=tf.summary.FileWriter(logs_train_dir, sess.graph)
saver=tf.train.Saver()
sess.run(tf.global_variables_initializer())
coord=tf.train.Coordinator()
threads=tf.train.start_queue_runners(sess=sess, coord=coord)

#进行训练
try:
    for step in np.arange(MAX_STEP):
        if coord.should_stop():
            break
        _, tra_loss, tra_acc=sess.run([train_op, train_loss, train_acc])
        #每隔10步打印一次当前的loss以及acc
        if step % 10 == 0:
            print('Step %d now,train loss is %.2f,train accuracy is %.2f%%.' % (step, tra_loss, tra_acc * 100.0))
            summary_str=sess.run(summary_op)
            train_writer.add_summary(summary_str, step)
        if ((step + 1) == MAX_STEP):
            checkpoint_path=os.path.join(logs_train_dir, 'model.ckpt')
            saver.save(sess, checkpoint_path, global_step=step)
except tf.errors.OutOfRangeError:
    print('Done!')
finally:
    coord.request_stop()