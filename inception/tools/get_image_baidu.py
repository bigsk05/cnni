import requests,re,os,urllib,json,sys
root=sys.argv[1]
depth=sys.argv[2]
jsonAddr=sys.argv[3]
class get_img_baidu(object):
    def get_page_html(self, page_url):
        headers = {
            'Referer': 'https://image.baidu.com/search/index?tn=baiduimage',
            'User-Agent': 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36'
        }
        try:
            r = requests.get(page_url, headers=headers)
            if r.status_code == 200:
                r.encoding = r.apparent_encoding
                return r.text
            else:
                print('Fail to requests baidu.')
        except Exception as e:
            print(e)
    def parse_result(self, text):
        url_real = re.findall('"thumbURL":"(.*?)",', text)
        return url_real
    def get_image_content(self, url_real):
        headers = {
            'Referer': url_real,
            'User-Agent': 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36'
        }
        try:
            r = requests.get(url_real, headers=headers)
            if r.status_code == 200:
                r.encoding = r.apparent_encoding
                return r.content
            else:
                print('Fail to requests baidu.')
        except Exception as e:
            print(e)
    def save_pic(self, url_real, content, root):
        path = root + url_real.split('/')[-1]
        if not os.path.exists(root):
            os.mkdir(root)
        if not os.path.exists(path):
            with open(path, 'wb') as f:
                f.write(content)
                print('Saved Picture {} in {}'.format(url_real, path))
        else:
            pass
    def get(self, keyword, depth, root):
        if("\\" not in root):
            root=root+"\\"
        keyword_quote = urllib.parse.quote(keyword)
        for i in range(depth):
            url = 'https://image.baidu.com/search/acjson?tn=resultjson_com&ipn=rj&ct=201326592&is=&fp=result&queryWord+=&cl=2&lm=-1&ie=utf-8&oe=utf-8&adpicid=&st=-1&word={}&z=&ic=0&s=&se=&tab=&width=&height=&face=0&istype=2&qc=&nc=1&fr=&step_word={}&pn={}&rn=30&gsm=1e&1541136876386='.format(
                keyword_quote, keyword_quote, i * 30)
            html = self.get_page_html(url)
            real_urls = self.parse_result(html)
            for real_url in real_urls:
                content = self.get_image_content(real_url)
                self.save_pic(real_url, content, root)

def main():
    global root
    global depth
    global jsonAddr
    if(os.path.isdir(root)):
        if(os.path.isdir(root)):
            pass
        else:
            os.mkdir(root)
    else:
        os.mkdir(root)
        os.mkdir(root)
    with open(jsonAddr,encoding="utf8") as fo:
        jsons=json.loads(fo.read())
    for i in range(0,len(jsons)):
        get_img_baidu().get(jsons[i],depth,root+'\\'+jsons[i]+'\\')
main()